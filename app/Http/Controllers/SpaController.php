<?php

namespace App\Http\Controllers;

use App\Models\News;
use App\Models\Speakers;
use App\Search\TextNormalizer;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Throwable;

/**
 * Serves the React app and adds per-page title / Open Graph tags, so links shared on
 * WhatsApp, Facebook, Telegram, X… show a preview card (their crawlers don't run JS).
 */
class SpaController extends Controller
{
    private const SITE_NAME = 'مؤتمر بوينت العراق';

    private const HOME_TITLE = 'مؤتمر بوينت العراق | Point Iraq Conference';

    private const DEFAULT_DESCRIPTION = 'مؤتمر بوينت العراق السابع – النسخة الإقليمية: ما بعد الليبرالية. المتحدثون وجدول الأعمال والأخبار والفيديوهات.';

    private const DEFAULT_IMAGE = '/images/og-point-iraq.jpg';

    // WhatsApp silently drops preview images much larger than this.
    private const MAX_IMAGE_BYTES = 600 * 1024;

    private const SECTIONS = [
        'news' => 'الأخبار',
        'speakers' => 'المتحدثون',
        'programs' => 'جدول الأعمال',
        'stream' => 'مقاطع الفيديو',
        'conference' => 'المؤتمر',
        'search' => 'البحث',
        'voting' => 'التصويت',
    ];

    public function __invoke(Request $request)
    {
        return view('app', ['meta' => $this->meta($request)]);
    }

    private function meta(Request $request): array
    {
        $base = rtrim(config('app.url'), '/');
        $path = trim($request->path(), '/');

        $meta = [
            'title' => self::HOME_TITLE,
            'description' => self::DEFAULT_DESCRIPTION,
            'image' => $base . self::DEFAULT_IMAGE,
            'image_width' => 1200,
            'image_height' => 630,
            'type' => 'website',
            'url' => $base . ($path === '' ? '/' : '/' . $path),
        ];

        $segments = $request->segments();

        try {
            if (count($segments) === 1 && isset(self::SECTIONS[$segments[0]])) {
                $meta['title'] = self::SECTIONS[$segments[0]] . ' | ' . self::SITE_NAME;
            } elseif (count($segments) === 2 && ctype_digit($segments[1])) {
                if ($segments[0] === 'speakers' && ($speaker = Speakers::find((int) $segments[1]))) {
                    $name = $speaker->name_ar ?: $speaker->name_en;
                    $role = collect([$speaker->job_ar ?: $speaker->job_en, $speaker->country_ar ?: $speaker->country_en])
                        ->map(fn ($v) => TextNormalizer::plainText($v))->filter()->implode(' · ');

                    $meta['title'] = trim($name) . ' | ' . self::SITE_NAME;
                    $meta['description'] = Str::limit($role ?: TextNormalizer::plainText($speaker->desc_ar ?: $speaker->desc_en), 200);
                    $meta['type'] = 'profile';
                    $meta = $this->withImage($meta, $speaker->image, $base);
                } elseif ($segments[0] === 'news' && ($news = News::find((int) $segments[1]))) {
                    $meta['title'] = trim($news->title_ar ?: $news->title_en) . ' | ' . self::SITE_NAME;
                    $meta['description'] = Str::limit(TextNormalizer::plainText($news->desc_ar ?: $news->desc_en), 200);
                    $meta['type'] = 'article';
                    $meta = $this->withImage($meta, $news->image, $base);
                }
            }
        } catch (Throwable $e) {
            report($e);
        }

        return $meta;
    }

    private function withImage(array $meta, ?string $image, string $base): array
    {
        $file = $image ? storage_path('app/public/' . $image) : null;
        $size = $file && is_file($file) ? @getimagesize($file) : false;

        if (! $size) {
            return $meta;
        }

        if (filesize($file) <= self::MAX_IMAGE_BYTES) {
            return array_merge($meta, [
                'image' => $base . '/api/images/' . rawurlencode($image),
                'image_width' => $size[0],
                'image_height' => $size[1],
            ]);
        }

        // The resized copy may be EXIF-rotated, so its final dimensions aren't advertised.
        return array_merge($meta, [
            'image' => $base . '/api/og-image/' . rawurlencode($image),
            'image_width' => null,
            'image_height' => null,
        ]);
    }
}
