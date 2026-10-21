<?php

namespace App\Http\Controllers;

/**
 * Serves a small JPEG copy of an uploaded image for link previews. WhatsApp ignores
 * preview images over ~600 KB, and many news photos are several MB.
 */
class OgImageController extends Controller
{
    private const MAX_WIDTH = 1200;

    private const FALLBACK = '/images/og-point-iraq.jpg';

    public function __invoke(string $filename)
    {
        abort_unless(preg_match('/^[A-Za-z0-9][A-Za-z0-9._-]{0,200}$/', $filename), 404);

        $source = storage_path('app/public/' . $filename);
        abort_unless(is_file($source), 404);

        $cached = storage_path('app/og-cache/' . pathinfo($filename, PATHINFO_FILENAME) . '.jpg');

        if ((! is_file($cached) || filemtime($cached) < filemtime($source)) && ! $this->render($source, $cached)) {
            return redirect(self::FALLBACK);
        }

        return response()->file($cached, [
            'Content-Type' => 'image/jpeg',
            'Cache-Control' => 'public, max-age=604800',
        ]);
    }

    private function render(string $source, string $target): bool
    {
        $info = @getimagesize($source);
        if (! $info) {
            return false;
        }

        $image = match ($info[2]) {
            IMAGETYPE_JPEG => @imagecreatefromjpeg($source),
            IMAGETYPE_PNG => @imagecreatefrompng($source),
            IMAGETYPE_WEBP => function_exists('imagecreatefromwebp') ? @imagecreatefromwebp($source) : false,
            default => false,
        };
        if (! $image) {
            return false;
        }

        // Phone photos store rotation in EXIF; GD ignores it, so apply it before resizing.
        if ($info[2] === IMAGETYPE_JPEG && function_exists('exif_read_data')) {
            $angle = match ((int) (@exif_read_data($source)['Orientation'] ?? 1)) {
                3 => 180,
                6 => -90,
                8 => 90,
                default => 0,
            };
            if ($angle !== 0 && ($rotated = imagerotate($image, $angle, 0))) {
                $image = $rotated;
            }
        }

        [$width, $height] = [imagesx($image), imagesy($image)];
        $scale = min(1, self::MAX_WIDTH / max(1, $width));
        [$newWidth, $newHeight] = [max(1, (int) round($width * $scale)), max(1, (int) round($height * $scale))];

        $canvas = imagecreatetruecolor($newWidth, $newHeight);
        imagefill($canvas, 0, 0, imagecolorallocate($canvas, 255, 255, 255));
        imagecopyresampled($canvas, $image, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);

        if (! is_dir(dirname($target)) && ! @mkdir(dirname($target), 0755, true)) {
            return false;
        }

        $temporary = $target . '.' . getmypid() . '.tmp';
        $saved = imagejpeg($canvas, $temporary, 80) && rename($temporary, $target);
        @unlink($temporary);

        return $saved;
    }
}
