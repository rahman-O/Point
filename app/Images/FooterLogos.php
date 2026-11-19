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

    // Roughly how many pixels are sampled when measuring a logo's ink coverage.
    private const INK_SAMPLES = 40000;

    /**
     * @return array{src: string, width: int, height: int, ink: float}|null
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
            'ink' => self::ink($path),
        ];
    }

    /**
     * Share of the white copy's box that is actually painted (0..1). Thin line-art logos
     * are around 0.2, solid badges around 0.5; the footer uses this to size logos so they
     * look equally heavy. Measured once per rendered copy and kept in a sidecar file.
     */
    private static function ink(string $path): float
    {
        $sidecar = $path . '.ink';
        if (is_file($sidecar) && filemtime($sidecar) >= filemtime($path)) {
            $cached = (float) file_get_contents($sidecar);
            if ($cached > 0) {
                return $cached;
            }
        }

        $image = @imagecreatefrompng($path);
        if (! $image) {
            return 0.3;
        }

        [$width, $height] = [imagesx($image), imagesy($image)];
        $step = max(1, (int) ceil(sqrt($width * $height / self::INK_SAMPLES)));
        $painted = 0;
        $sampled = 0;
        for ($y = 0; $y < $height; $y += $step) {
            for ($x = 0; $x < $width; $x += $step) {
                $painted += 1 - ((imagecolorat($image, $x, $y) >> 24) & 0x7F) / 127;
                $sampled++;
            }
        }
        $ink = round(max(0.01, $painted / max(1, $sampled)), 3);

        @file_put_contents($sidecar, (string) $ink);

        return $ink;
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
