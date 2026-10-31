<?php

namespace App\Images;

/**
 * White copies of organizer/partner logos for the dark site footer. Each copy is rendered
 * once and cached under storage/app/footer-logos; the uploaded originals are never changed.
 */
final class FooterLogos
{
    // Bump after changing WhiteLogo so the cached copies are rendered again.
    private const VERSION = 1;

    /**
     * @return array{src: string, width: int, height: int}|null
     */
    public static function for(?string $image): ?array
    {
        $path = self::path($image);
        $size = $path ? @getimagesize($path) : false;
        if (! $size) {
            return null;
        }

        return [
            'src' => '/api/footer-logos/' . rawurlencode($image) . '?v=' . filemtime($path),
            'width' => $size[0],
            'height' => $size[1],
        ];
    }

    /**
     * Path of the white copy of an uploaded image, rendering it first when needed.
     */
    public static function path(?string $image): ?string
    {
        if (! $image || ! preg_match('/^[A-Za-z0-9][A-Za-z0-9._-]{0,200}$/', $image)) {
            return null;
        }

        $source = storage_path('app/public/' . $image);
        if (! is_file($source)) {
            return null;
        }

        $cached = storage_path('app/footer-logos/v' . self::VERSION . '/' . $image . '.png');
        $failed = $cached . '.failed';
        $modified = filemtime($source);

        if (is_file($cached) && filemtime($cached) >= $modified) {
            return $cached;
        }
        // Images that can't be converted are not retried until they change.
        if (is_file($failed) && filemtime($failed) >= $modified) {
            return null;
        }

        if (WhiteLogo::render($source, $cached)) {
            return $cached;
        }

        @mkdir(dirname($failed), 0755, true);
        @touch($failed);

        return null;
    }
}
