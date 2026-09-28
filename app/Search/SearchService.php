<?php

namespace App\Search;

use App\Models\SearchEntry;
use Illuminate\Database\Eloquent\Builder;

class SearchService
{
    private const TYPE_WEIGHT = "CASE entry_type WHEN 'speaker' THEN 4 WHEN 'session' THEN 3 WHEN 'conference' THEN 2 WHEN 'news' THEN 1 ELSE 0 END";

    private const SNIPPET_WORDS = 28;

    // Terms this short ("of", "or", "1") only match whole words; otherwise they hit inside most words.
    private const SHORT_TERM = 2;

    public function search(string $query, ?string $type, string $lang, int $page = 1, int $limit = 20): array
    {
        $terms = TextNormalizer::terms($query);
        $response = [
            'query' => $query,
            'terms' => $terms,
            'relaxed' => false,
            'total' => 0,
            'counts' => array_fill_keys(array_keys(SearchIndexer::MODELS), 0),
            'page' => $page,
            'has_more' => false,
            'results' => [],
        ];

        if (! $terms) {
            return $response;
        }

        $counts = $this->counts($terms, true);
        if (array_sum($counts) === 0 && count($terms) > 1) {
            // Nothing contains every word: fall back to entries matching any word, ranked by how many they hit.
            $response['relaxed'] = true;
            $counts = $this->counts($terms, false);
        }

        $response['counts'] = array_merge($response['counts'], $counts);
        $response['total'] = $type ? ($counts[$type] ?? 0) : array_sum($counts);

        if ($response['total'] === 0) {
            return $response;
        }

        [$scoreSql, $scoreBindings] = $this->scoreExpression($terms);

        $rows = $this->matching($terms, ! $response['relaxed'])
            ->when($type, fn ($q) => $q->where('entry_type', $type))
            ->select([
                'id', 'entry_type', 'entry_id', 'title_en', 'title_ar', 'subtitle_en', 'subtitle_ar',
                'content_en', 'content_ar', 'image', 'url', 'year', 'published_on', 'extra',
            ])
            ->selectRaw("({$scoreSql}) AS score", $scoreBindings)
            ->orderByDesc('score')
            ->orderByRaw('published_on IS NULL')
            ->orderByDesc('published_on')
            ->orderByDesc('id')
            ->forPage($page, $limit)
            ->get();

        $response['has_more'] = $page * $limit < $response['total'];
        $response['results'] = $rows->map(fn (SearchEntry $row) => $this->present($row, $terms, $lang))->all();

        return $response;
    }

    private function matching(array $terms, bool $matchAll): Builder
    {
        return SearchEntry::query()->where(function (Builder $where) use ($terms, $matchAll) {
            foreach ($terms as $term) {
                [$sql, $binding] = $this->termCondition('search_all', $term);
                $where->whereRaw($sql, [$binding], $matchAll ? 'and' : 'or');
            }
        });
    }

    private function termCondition(string $column, string $term): array
    {
        $escaped = $this->escapeLike($term);

        return mb_strlen($term) <= self::SHORT_TERM
            ? ["CONCAT(' ', {$column}, ' ') LIKE ? ESCAPE '!'", "% {$escaped} %"]
            : ["{$column} LIKE ? ESCAPE '!'", "%{$escaped}%"];
    }

    private function counts(array $terms, bool $matchAll): array
    {
        return $this->matching($terms, $matchAll)
            ->selectRaw('entry_type, COUNT(*) AS aggregate')
            ->groupBy('entry_type')
            ->pluck('aggregate', 'entry_type')
            ->map(fn ($c) => (int) $c)
            ->all();
    }

    /**
     * Weighted relevance: whole-phrase title hits beat title prefixes, which beat
     * per-word title hits, metadata hits (job, author, speakers, year) and body text.
     */
    private function scoreExpression(array $terms): array
    {
        $phrase = $this->escapeLike(implode(' ', $terms));
        $parts = [
            "CASE WHEN CONCAT(' ', search_title, ' ') LIKE ? ESCAPE '!' THEN 60 ELSE 0 END",
            "CASE WHEN search_title LIKE ? ESCAPE '!' OR search_title LIKE ? ESCAPE '!' THEN 30 ELSE 0 END",
            "CASE WHEN search_title LIKE ? ESCAPE '!' THEN 25 ELSE 0 END",
            "CASE WHEN search_all LIKE ? ESCAPE '!' THEN 6 ELSE 0 END",
        ];
        $bindings = ["% {$phrase} %", "{$phrase}%", "%| {$phrase}%", "%{$phrase}%", "%{$phrase}%"];

        foreach ($terms as $term) {
            foreach (['search_title' => 10, 'search_meta' => 4, 'search_all' => 3] as $column => $weight) {
                [$sql, $binding] = $this->termCondition($column, $term);
                $parts[] = "CASE WHEN {$sql} THEN {$weight} ELSE 0 END";
                $bindings[] = $binding;
            }

            if (mb_strlen($term) > self::SHORT_TERM) {
                $parts[] = "CASE WHEN CONCAT(' ', search_title) LIKE ? ESCAPE '!' THEN 5 ELSE 0 END";
                $bindings[] = '% ' . $this->escapeLike($term) . '%';
            }
        }

        $parts[] = self::TYPE_WEIGHT;

        return [implode(' + ', $parts), $bindings];
    }

    private function escapeLike(string $value): string
    {
        return str_replace(['!', '%', '_'], ['!!', '!%', '!_'], $value);
    }

    private function present(SearchEntry $row, array $terms, string $lang): array
    {
        $other = $lang === 'ar' ? 'en' : 'ar';
        $title = $row->{"title_{$lang}"} ?: $row->{"title_{$other}"} ?: '';
        $altTitle = $row->{"title_{$other}"};
        $subtitle = $row->{"subtitle_{$lang}"} ?: $row->{"subtitle_{$other}"};

        $snippet = $this->snippet($row->{"content_{$lang}"}, $terms)
            ?? $this->snippet($row->{"content_{$other}"}, $terms)
            ?? $this->snippet($row->{"content_{$lang}"} ?: $row->{"content_{$other}"}, [], true);

        $image = $row->image;
        if ($image && ! str_starts_with($image, 'https://')) {
            $image = '/api/images/' . ltrim($image, '/');
        }

        $titleSegments = $this->segments($title, $terms);
        $titleMatched = in_array(true, array_column($titleSegments, 'match'), true);
        $altSegments = ! $titleMatched && $altTitle && $altTitle !== $title ? $this->segments($altTitle, $terms) : null;

        $snippetText = TextNormalizer::normalize(implode('', array_column($snippet, 'text')));
        $normalizedTitle = TextNormalizer::normalize($title);
        if ($normalizedTitle !== '' && (str_starts_with($snippetText, $normalizedTitle) || str_contains($normalizedTitle, $snippetText))) {
            $snippet = [];
        }

        return [
            'type' => $row->entry_type,
            'id' => $row->entry_id,
            'url' => $row->url,
            'external' => str_starts_with($row->url, 'http'),
            'title' => $titleSegments,
            'alt_title' => $altSegments && in_array(true, array_column($altSegments, 'match'), true) ? $altSegments : null,
            'subtitle' => $subtitle ? $this->segments($subtitle, $terms) : null,
            'snippet' => $snippet,
            'image' => $image,
            'year' => $row->year,
            'date' => $row->published_on?->toDateString(),
            'extra' => $row->extra,
        ];
    }

    /**
     * Picks a window of words around the first match. Returns null when $terms are
     * given but none occur, so the caller can try the other language.
     */
    private function snippet(?string $text, array $terms, bool $fallback = false): ?array
    {
        if (! $text) {
            return $fallback ? [] : null;
        }

        $words = preg_split('/\s+/u', $text, -1, PREG_SPLIT_NO_EMPTY);
        $hit = null;

        foreach (array_slice($words, 0, 4000) as $i => $word) {
            if ($this->matches($word, $terms)) {
                $hit = $i;
                break;
            }
        }

        if ($hit === null && ! $fallback) {
            return null;
        }

        $start = max(0, ($hit ?? 0) - 8);
        $window = array_slice($words, $start, self::SNIPPET_WORDS);
        $text = ($start > 0 ? '… ' : '') . implode(' ', $window) . ($start + self::SNIPPET_WORDS < count($words) ? ' …' : '');

        return $this->segments($text, $terms);
    }

    /**
     * Splits text into [{text, match}] segments so the client can highlight without rendering HTML.
     */
    private function segments(string $text, array $terms): array
    {
        $segments = [];
        $push = function (string $piece, bool $match) use (&$segments) {
            if ($piece === '') {
                return;
            }
            $last = array_key_last($segments);
            if ($last !== null && $segments[$last]['match'] === $match) {
                $segments[$last]['text'] .= $piece;
            } else {
                $segments[] = ['text' => $piece, 'match' => $match];
            }
        };

        foreach (preg_split('/(\s+)/u', $text, -1, PREG_SPLIT_DELIM_CAPTURE | PREG_SPLIT_NO_EMPTY) as $token) {
            if (trim($token) === '' || ! $this->matches($token, $terms)) {
                $push($token, false);
                continue;
            }

            // Keep surrounding punctuation ("Iraq?", "(2018),") outside the highlight.
            preg_match('/^([^\p{L}\p{N}]*)(.*?)([^\p{L}\p{N}]*)$/us', $token, $parts);
            $push($parts[1] ?? '', false);
            $push($parts[2] ?? $token, true);
            $push($parts[3] ?? '', false);
        }

        return $segments;
    }

    private function matches(string $word, array $terms): bool
    {
        if (! $terms) {
            return false;
        }

        $normalized = TextNormalizer::normalize($word);
        if ($normalized === '') {
            return false;
        }

        $pieces = explode(' ', $normalized);

        foreach ($terms as $term) {
            $isShort = mb_strlen($term) <= self::SHORT_TERM;
            if ($isShort ? in_array($term, $pieces, true) : str_contains($normalized, $term)) {
                return true;
            }
        }

        return false;
    }
}
