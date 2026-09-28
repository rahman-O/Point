<?php

namespace App\Search;

use App\Models\Conference;
use App\Models\News;
use App\Models\SearchEntry;
use App\Models\SessionsProgram;
use App\Models\Speakers;
use App\Models\Stream;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Throwable;

class SearchIndexer
{
    public const MODELS = [
        'speaker' => Speakers::class,
        'session' => SessionsProgram::class,
        'news' => News::class,
        'stream' => Stream::class,
        'conference' => Conference::class,
    ];

    private const CONTENT_LIMIT = 20000;

    /** @var array<string, Model> */
    private static array $pending = [];

    private static bool $flushRegistered = false;

    /**
     * Defers reindexing until the response is sent, so relationships saved after the
     * model itself (e.g. session speakers in Filament) are included.
     */
    public static function queue(Model $model): void
    {
        $type = array_search($model::class, self::MODELS, true);
        if ($type === false) {
            return;
        }

        self::$pending[$type . ':' . $model->getKey()] = $model;

        if (! self::$flushRegistered) {
            self::$flushRegistered = true;
            app()->terminating(function () {
                $models = self::$pending;
                self::$pending = [];
                self::$flushRegistered = false;

                foreach ($models as $model) {
                    try {
                        app(self::class)->sync($model);
                    } catch (Throwable $e) {
                        report($e);
                    }
                }
            });
        }
    }

    public function sync(Model $model): void
    {
        $type = array_search($model::class, self::MODELS, true);
        if ($type === false) {
            return;
        }

        $fresh = $this->query($type)->find($model->getKey());

        if ($fresh) {
            $this->store($type, $fresh);
        } else {
            SearchEntry::where('entry_type', $type)->where('entry_id', $model->getKey())->delete();
        }

        // Session entries embed speaker names, so keep them in step with speaker edits.
        if ($type === 'speaker') {
            $sessions = $this->query('session')
                ->where('facilitator_id', $model->getKey())
                ->orWhereHas('speakers', fn ($q) => $q->whereKey($model->getKey()))
                ->get();

            foreach ($sessions as $session) {
                $this->store('session', $session);
            }
        }
    }

    public function rebuild(): int
    {
        return DB::transaction(function () {
            $count = 0;

            foreach (array_keys(self::MODELS) as $type) {
                $ids = [];
                $this->query($type)->chunkById(100, function ($models) use ($type, &$ids, &$count) {
                    foreach ($models as $model) {
                        $this->store($type, $model);
                        $ids[] = $model->getKey();
                        $count++;
                    }
                });

                SearchEntry::where('entry_type', $type)->whereNotIn('entry_id', $ids ?: [0])->delete();
            }

            SearchEntry::whereNotIn('entry_type', array_keys(self::MODELS))->delete();

            return $count;
        });
    }

    private function query(string $type)
    {
        $query = (self::MODELS[$type])::query();

        return $type === 'session' ? $query->with(['speakers', 'facilitator']) : $query;
    }

    private function store(string $type, Model $model): void
    {
        $entry = $this->{'build' . ucfirst($type)}($model);

        $searchTitle = TextNormalizer::normalize($entry['title_en']) . ' | ' . TextNormalizer::normalize($entry['title_ar']);
        $searchMeta = TextNormalizer::normalize(implode(' ', array_filter([
            $entry['subtitle_en'], $entry['subtitle_ar'], $entry['meta'] ?? null, $entry['year'],
        ])));
        $searchAll = TextNormalizer::normalize(implode(' ', array_filter([
            $entry['title_en'], $entry['title_ar'], $entry['subtitle_en'], $entry['subtitle_ar'],
            $entry['meta'] ?? null, $entry['year'], $entry['content_en'], $entry['content_ar'],
        ])));

        unset($entry['meta']);

        SearchEntry::updateOrCreate(
            ['entry_type' => $type, 'entry_id' => $model->getKey()],
            $entry + [
                'search_title' => $searchTitle,
                'search_meta' => $searchMeta,
                'search_all' => $searchAll,
            ],
        );
    }

    private function buildSpeaker(Speakers $speaker): array
    {
        return [
            'title_en' => $this->text($speaker->name_en, 500),
            'title_ar' => $this->text($speaker->name_ar, 500),
            'subtitle_en' => $this->join([$speaker->job_en, $speaker->country_en]),
            'subtitle_ar' => $this->join([$speaker->job_ar, $speaker->country_ar]),
            'content_en' => $this->text($speaker->desc_en),
            'content_ar' => $this->text($speaker->desc_ar),
            'image' => $speaker->image ?: null,
            'url' => '/speakers/' . $speaker->getKey(),
            'year' => $this->text($speaker->year, 10) ?: null,
            'published_on' => null,
            'extra' => null,
        ];
    }

    private function buildNews(News $news): array
    {
        $date = $this->date($news->event_time);

        return [
            'title_en' => $this->text($news->title_en, 500),
            'title_ar' => $this->text($news->title_ar, 500),
            'subtitle_en' => $this->text($news->author_en, 500) ?: null,
            'subtitle_ar' => $this->text($news->author_ar, 500) ?: null,
            'content_en' => $this->text($news->desc_en),
            'content_ar' => $this->text($news->desc_ar),
            'image' => $news->image ?: null,
            'url' => '/news/' . $news->getKey(),
            'year' => $date ? (string) $date->year : null,
            'published_on' => $date,
            'extra' => null,
        ];
    }

    private function buildSession(SessionsProgram $session): array
    {
        $people = collect($session->speakers->all())->when($session->facilitator, fn ($c) => $c->push($session->facilitator));
        $namesEn = $this->join($people->pluck('name_en')->all(), ', ');
        $namesAr = $this->join($people->pluck('name_ar')->all(), '، ');
        $day = preg_match('/^day([1-9])$/', (string) $session->day, $m) ? (int) $m[1] : null;

        return [
            'title_en' => $this->text($session->title_en, 500) ?: $this->text($session->subject_en, 500),
            'title_ar' => $this->text($session->title_ar, 500) ?: $this->text($session->subject_ar, 500),
            'subtitle_en' => $namesEn,
            'subtitle_ar' => $namesAr,
            'content_en' => $this->join([$session->subject_en, $session->presentation_en], ' ') ?: null,
            'content_ar' => $this->join([$session->subject_ar, $session->presentation_ar], ' ') ?: null,
            'image' => null,
            'url' => '/programs' . ($day ? '?day=day' . $day : ''),
            'year' => $this->text($session->year, 10) ?: null,
            'published_on' => null,
            'meta' => $day ? "day {$day} اليوم {$day}" : null,
            'extra' => [
                'day' => $day,
                'start' => $session->start_time ? substr($session->start_time, 0, 5) : null,
                'end' => $session->end_time ? substr($session->end_time, 0, 5) : null,
            ],
        ];
    }

    private function buildStream(Stream $stream): array
    {
        $date = $this->date($stream->date);
        $isHttpUrl = (bool) preg_match('~^https?://~i', (string) $stream->url);

        return [
            'title_en' => $this->text($stream->title_en, 500),
            'title_ar' => $this->text($stream->title_ar, 500),
            'subtitle_en' => null,
            'subtitle_ar' => null,
            'content_en' => null,
            'content_ar' => null,
            'image' => $stream->youtube_video_id
                ? 'https://img.youtube.com/vi/' . rawurlencode($stream->youtube_video_id) . '/mqdefault.jpg'
                : null,
            'url' => $isHttpUrl ? mb_substr($stream->url, 0, 1000) : '/stream',
            'year' => $date ? (string) $date->year : null,
            'published_on' => $date,
            'extra' => null,
        ];
    }

    private function buildConference(Conference $conference): array
    {
        return [
            'title_en' => $this->text($conference->title_en, 500),
            'title_ar' => $this->text($conference->title_ar, 500),
            'subtitle_en' => null,
            'subtitle_ar' => null,
            'content_en' => $this->text($conference->desc_en),
            'content_ar' => $this->text($conference->desc_ar),
            'image' => $conference->image ?: null,
            'url' => '/conference',
            'year' => null,
            'published_on' => null,
            'extra' => null,
        ];
    }

    private function text(?string $value, int $limit = self::CONTENT_LIMIT): string
    {
        return mb_substr(TextNormalizer::plainText($value), 0, $limit);
    }

    private function join(array $parts, string $glue = ' · '): ?string
    {
        $parts = array_filter(array_map(fn ($p) => $this->text($p, 500), $parts), fn ($p) => $p !== '');

        return $parts ? mb_substr(implode($glue, array_unique($parts)), 0, 500) : null;
    }

    private function date($value): ?Carbon
    {
        try {
            return $value ? Carbon::parse($value)->startOfDay() : null;
        } catch (Throwable) {
            return null;
        }
    }
}
