<?php

namespace App\Search;

use Normalizer;

/**
 * Turns Arabic/English text into a canonical form so that the index and the
 * user's query compare equal regardless of hamza forms, taa marbuta, diacritics,
 * tatweel, letter case, accents or Arabic-Indic digits.
 */
final class TextNormalizer
{
    private const CHARACTER_MAP = [
        'أ' => 'ا', 'إ' => 'ا', 'آ' => 'ا', 'ٱ' => 'ا', 'ٲ' => 'ا', 'ٳ' => 'ا',
        'ؤ' => 'و', 'ئ' => 'ي', 'ى' => 'ي', 'ی' => 'ي', 'ة' => 'ه', 'ک' => 'ك',
        '٠' => '0', '١' => '1', '٢' => '2', '٣' => '3', '٤' => '4',
        '٥' => '5', '٦' => '6', '٧' => '7', '٨' => '8', '٩' => '9',
        '۰' => '0', '۱' => '1', '۲' => '2', '۳' => '3', '۴' => '4',
        '۵' => '5', '۶' => '6', '۷' => '7', '۸' => '8', '۹' => '9',
        '٫' => '.',
    ];

    public static function plainText(?string $html): string
    {
        if ($html === null || $html === '') {
            return '';
        }

        $text = preg_replace('~<(script|style)\b[^>]*>.*?</\1>~is', ' ', $html) ?? $html;
        $text = preg_replace('~<[^>]*>~', ' ', $text) ?? $text;
        $text = html_entity_decode($text, ENT_QUOTES | ENT_HTML5, 'UTF-8');
        $text = str_replace("\u{00A0}", ' ', $text);

        return trim(preg_replace('/\s+/u', ' ', $text) ?? $text);
    }

    public static function normalize(?string $text): string
    {
        if ($text === null || $text === '') {
            return '';
        }

        $text = mb_strtolower($text, 'UTF-8');

        if (class_exists(Normalizer::class)) {
            $text = Normalizer::normalize($text, Normalizer::FORM_D) ?: $text;
        }

        // Combining marks cover Arabic harakat, hamza/madda marks and Latin accents; U+0640 is tatweel.
        $text = preg_replace('/[\p{Mn}\x{0640}]/u', '', $text) ?? $text;
        $text = strtr($text, self::CHARACTER_MAP);

        // Keep decimal points inside numbers ("5.0"), turn every other symbol into a word break.
        $text = preg_replace('/[^\p{L}\p{N}.]+/u', ' ', $text) ?? $text;
        $text = preg_replace('/(?<!\d)\.|\.(?!\d)/u', ' ', $text) ?? $text;

        return trim(preg_replace('/\s+/u', ' ', $text) ?? $text);
    }

    /**
     * @return list<string>
     */
    public static function terms(string $query, int $max = 6): array
    {
        $words = explode(' ', self::normalize($query));

        return array_slice(array_values(array_unique(array_filter($words, fn ($w) => $w !== ''))), 0, $max);
    }
}
