<?php

namespace App\Images;

use GdImage;

/**
 * Makes a white, transparent, tightly cropped PNG copy of an uploaded logo, so every logo
 * reads clearly on the black site footer whatever its original colours.
 *
 * The background is removed (transparent, or a solid colour such as the white box of a JPEG)
 * and every remaining mark is painted white. Light details enclosed by a coloured shape (a
 * white icon inside a green badge) are cut out so the shape keeps its detail, while a dark
 * plate carrying light lettering (white text on a black box) drops out instead, as the
 * footer itself is black.
 */
final class WhiteLogo
{
    // The output fits in this box: enough for the footer on 3x screens.
    private const MAX_WIDTH = 600;

    private const MAX_HEIGHT = 240;

    // Pixels are classified at twice the output size, then downsampled for smooth edges.
    private const SUPERSAMPLE = 2;

    // How far (in working pixels) to look around a light area, past its soft edge.
    private const LOOK_AROUND = 6;

    private const BACKGROUND = 0;

    private const LIGHT = 1;

    private const DARK = 2;

    private const COLOR = 3;

    /**
     * @return array{0: int, 1: int}|null Size of the PNG written to $target, or null when the
     *                                    image can't be read or has no clear background.
     */
    public static function render(string $source, string $target): ?array
    {
        $image = self::load($source);
        if (! $image) {
            return null;
        }

        $image = self::fit($image, 2000, 2000);
        $background = self::background($image);
        if ($background === false) {
            return null;
        }

        $box = self::box($image, fn (int $argb): bool => self::coverage($argb, $background) >= 0.1);
        if (! $box) {
            return null;
        }

        $image = self::fit(imagecrop($image, $box), self::MAX_WIDTH * self::SUPERSAMPLE, self::MAX_HEIGHT * self::SUPERSAMPLE, 2.0);
        $logo = self::fit(self::paint($image, $background), self::MAX_WIDTH, self::MAX_HEIGHT);

        $box = self::box($logo, fn (int $argb): bool => (($argb >> 24) & 0x7F) < 122);
        if (! $box) {
            return null;
        }
        $logo = imagecrop($logo, $box);
        imagesavealpha($logo, true);

        if (! is_dir(dirname($target)) && ! @mkdir(dirname($target), 0755, true)) {
            return null;
        }

        $temporary = $target . '.' . getmypid() . '.tmp';
        $saved = imagepng($logo, $temporary, 9) && rename($temporary, $target);
        if (! $saved && is_file($temporary)) {
            unlink($temporary);
        }

        return $saved ? [imagesx($logo), imagesy($logo)] : null;
    }

    private static function load(string $path): ?GdImage
    {
        $info = @getimagesize($path);
        if (! $info || ! self::hasMemoryFor($info[0], $info[1])) {
            return null;
        }

        $image = match ($info[2]) {
            IMAGETYPE_PNG => @imagecreatefrompng($path),
            IMAGETYPE_JPEG => @imagecreatefromjpeg($path),
            IMAGETYPE_WEBP => function_exists('imagecreatefromwebp') ? @imagecreatefromwebp($path) : false,
            IMAGETYPE_GIF => @imagecreatefromgif($path),
            default => false,
        };

        if (! $image) {
            return null;
        }
        if (! imageistruecolor($image)) {
            imagepalettetotruecolor($image);
        }

        return $image;
    }

    // Decoding takes up to ~8 bytes per pixel, so very large uploads need a higher limit.
    private static function hasMemoryFor(int $width, int $height): bool
    {
        $needed = memory_get_usage() + $width * $height * 8 + 32 * 1024 * 1024;
        $limit = trim((string) ini_get('memory_limit'));
        $bytes = (int) $limit * match (strtolower(substr($limit, -1))) {
            'g' => 1024 ** 3,
            'm' => 1024 ** 2,
            'k' => 1024,
            default => 1,
        };

        return $limit === '-1' || $needed <= $bytes || ini_set('memory_limit', (string) $needed) !== false;
    }

    /**
     * @return array{0: int, 1: int, 2: int}|false|null The solid background colour, null when the
     *                                                   background is transparent, false when unclear.
     */
    private static function background(GdImage $image): array|false|null
    {
        [$width, $height] = [imagesx($image), imagesy($image)];
        $step = max(1, intdiv($width + $height, 1000));
        $edge = [];
        for ($x = 0; $x < $width; $x += $step) {
            $edge[] = imagecolorat($image, $x, 0);
            $edge[] = imagecolorat($image, $x, $height - 1);
        }
        for ($y = 0; $y < $height; $y += $step) {
            $edge[] = imagecolorat($image, 0, $y);
            $edge[] = imagecolorat($image, $width - 1, $y);
        }

        $opaque = array_values(array_filter($edge, fn (int $argb): bool => (($argb >> 24) & 0x7F) < 64));
        if (count($opaque) < count($edge) / 2) {
            return null;
        }

        $groups = [];
        foreach ($opaque as $argb) {
            $groups[(($argb >> 20) & 0xF) . '-' . (($argb >> 12) & 0xF) . '-' . (($argb >> 4) & 0xF)][] = $argb;
        }
        usort($groups, fn (array $a, array $b): int => count($b) <=> count($a));
        if (count($groups[0]) < 0.6 * count($opaque)) {
            return false;
        }

        $sum = [0, 0, 0];
        foreach ($groups[0] as $argb) {
            $sum[0] += ($argb >> 16) & 0xFF;
            $sum[1] += ($argb >> 8) & 0xFF;
            $sum[2] += $argb & 0xFF;
        }

        return array_map(fn (int $total): int => intdiv($total, count($groups[0])), $sum);
    }

    /**
     * How much of a pixel belongs to the logo rather than to the background (0..1).
     *
     * @param  array{0: int, 1: int, 2: int}|null  $background
     */
    private static function coverage(int $argb, ?array $background): float
    {
        $alpha = 1 - (($argb >> 24) & 0x7F) / 127;
        if ($background === null) {
            return $alpha;
        }

        $distance = sqrt(
            ((($argb >> 16) & 0xFF) - $background[0]) ** 2
            + ((($argb >> 8) & 0xFF) - $background[1]) ** 2
            + (($argb & 0xFF) - $background[2]) ** 2
        ) / 441.673;

        return $alpha * self::smoothstep(0.08, 0.25, $distance);
    }

    /**
     * @param  callable(int): bool  $isInk
     * @return array{x: int, y: int, width: int, height: int}|null
     */
    private static function box(GdImage $image, callable $isInk): ?array
    {
        [$width, $height] = [imagesx($image), imagesy($image)];
        $step = max(1, intdiv(max($width, $height), 600));
        [$left, $top, $right, $bottom] = [$width, $height, -1, -1];

        for ($y = 0; $y < $height; $y += $step) {
            for ($x = 0; $x < $width; $x += $step) {
                if ($isInk(imagecolorat($image, $x, $y))) {
                    [$left, $right] = [min($left, $x), max($right, $x)];
                    [$top, $bottom] = [min($top, $y), max($bottom, $y)];
                }
            }
        }

        if ($right < 0) {
            return null;
        }

        [$left, $top] = [max(0, $left - $step), max(0, $top - $step)];
        [$right, $bottom] = [min($width - 1, $right + $step), min($height - 1, $bottom + $step)];

        return ['x' => $left, 'y' => $top, 'width' => $right - $left + 1, 'height' => $bottom - $top + 1];
    }

    private static function fit(GdImage $image, int $maxWidth, int $maxHeight, float $maxScale = 1.0): GdImage
    {
        [$width, $height] = [imagesx($image), imagesy($image)];
        $scale = min($maxScale, $maxWidth / $width, $maxHeight / $height);
        if (abs($scale - 1) < 0.01) {
            return $image;
        }

        [$newWidth, $newHeight] = [max(1, (int) round($width * $scale)), max(1, (int) round($height * $scale))];
        $canvas = self::canvas($newWidth, $newHeight);
        imagecopyresampled($canvas, $image, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);

        return $canvas;
    }

    private static function canvas(int $width, int $height): GdImage
    {
        $canvas = imagecreatetruecolor($width, $height);
        imagealphablending($canvas, false);
        imagesavealpha($canvas, true);
        imagefill($canvas, 0, 0, imagecolorallocatealpha($canvas, 0, 0, 0, 127));

        return $canvas;
    }

    /**
     * @param  array{0: int, 1: int, 2: int}|null  $background
     */
    private static function paint(GdImage $image, ?array $background): GdImage
    {
        [$width, $height] = [imagesx($image), imagesy($image)];
        $count = $width * $height;
        $cover = $luma = $class = [];

        for ($i = 0; $i < $count; $i++) {
            $argb = imagecolorat($image, $i % $width, intdiv($i, $width));
            [$r, $g, $b] = [($argb >> 16) & 0xFF, ($argb >> 8) & 0xFF, $argb & 0xFF];
            $cover[$i] = self::coverage($argb, $background);
            $luma[$i] = (0.2126 * $r + 0.7152 * $g + 0.0722 * $b) / 255;
            $class[$i] = match (true) {
                $cover[$i] < 0.5 => self::BACKGROUND,
                $luma[$i] >= 0.8 && max($r, $g, $b) - min($r, $g, $b) <= 51 => self::LIGHT,
                $luma[$i] <= 0.2 => self::DARK,
                default => self::COLOR,
            };
        }

        $keep = self::keep($class, $luma, $width, $count);

        $canvas = self::canvas($width, $height);
        $colors = [];
        for ($i = 0; $i < $count; $i++) {
            $alpha = $cover[$i] * $keep[$i];
            if ($alpha < 0.02) {
                continue;
            }
            $level = 127 - (int) round($alpha * 127);
            $colors[$level] ??= imagecolorallocatealpha($canvas, 255, 255, 255, $level);
            imagesetpixel($canvas, $i % $width, intdiv($i, $width), $colors[$level]);
        }

        return $canvas;
    }

    /**
     * How much of each pixel stays in the white logo (0..1): light details inside a coloured
     * shape are cut out, and dark plates under light lettering drop out.
     *
     * @param  array<int, int>  $class
     * @param  array<int, float>  $luma
     * @return array<int, float>
     */
    private static function keep(array $class, array $luma, int $width, int $count): array
    {
        [$region, $members, $kinds] = self::regions($class, $width, $count);
        $knockouts = $plates = [];

        foreach ($kinds as $id => $kind) {
            if ($kind !== self::LIGHT || count($members[$id]) < 6) {
                continue;
            }

            [$backgroundShare, $around, $darkRegions] = self::surroundings($members[$id], $id, $region, $class, $luma, $width, $count);
            if ($backgroundShare >= 0.25 || ! $around) {
                continue; // A light mark of its own, e.g. a logo that is white already.
            }

            sort($around);
            if ($around[intdiv(count($around), 4)] < 0.3) {
                foreach ($darkRegions as $dark => $_) {
                    $plates[$dark] = ($plates[$dark] ?? 0) + count($members[$id]);
                }
            } else {
                $knockouts[] = $id;
            }
        }

        $keep = array_fill(0, $count, 1.0);
        foreach ($knockouts as $id) {
            self::leaveOut($keep, $members[$id], $region, $luma, $width, $count, 3, fn (float $l): float => 1 - self::smoothstep(0.65, 0.95, $l));
        }
        foreach ($plates as $dark => $lightArea) {
            if ($lightArea >= 0.04 * count($members[$dark])) {
                self::leaveOut($keep, $members[$dark], $region, $luma, $width, $count, 4, fn (float $l): float => self::smoothstep(0.25, 0.75, $l));
            }
        }

        return $keep;
    }

    /**
     * Groups connected light pixels and connected dark pixels into regions.
     *
     * @param  array<int, int>  $class
     * @return array{0: array<int, int>, 1: array<int, array<int, int>>, 2: array<int, int>}
     *                                                                                       The region of each pixel (-1 for none), and the pixels and kind of each region.
     */
    private static function regions(array $class, int $width, int $count): array
    {
        $region = array_fill(0, $count, -1);
        $members = $kinds = [];

        for ($start = 0; $start < $count; $start++) {
            $kind = $class[$start];
            if ($region[$start] !== -1 || ($kind !== self::LIGHT && $kind !== self::DARK)) {
                continue;
            }

            $id = count($members);
            $region[$start] = $id;
            $queue = [$start];
            for ($q = 0; isset($queue[$q]); $q++) {
                foreach (self::neighbours($queue[$q], $width, $count) as $j) {
                    if ($region[$j] === -1 && $class[$j] === $kind) {
                        $region[$j] = $id;
                        $queue[] = $j;
                    }
                }
            }
            $members[$id] = $queue;
            $kinds[$id] = $kind;
        }

        return [$region, $members, $kinds];
    }

    /**
     * Looks a few pixels around a light region, past its soft edge.
     *
     * @param  array<int, int>  $members
     * @param  array<int, int>  $region
     * @param  array<int, int>  $class
     * @param  array<int, float>  $luma
     * @return array{0: float, 1: array<int, float>, 2: array<int, true>} The share of background
     *                                                                    around it, the brightness of the logo around it and the dark regions it sits on.
     */
    private static function surroundings(array $members, int $id, array $region, array $class, array $luma, int $width, int $count): array
    {
        $seen = $frontier = $around = $darkRegions = [];
        $background = $total = 0;

        foreach ($members as $i) {
            $neighbours = self::neighbours($i, $width, $count);
            // Beyond the image edge counts as background.
            $background += 4 - count($neighbours);
            $total += 4 - count($neighbours);
            foreach ($neighbours as $j) {
                if ($region[$j] !== $id && ! isset($seen[$j])) {
                    $seen[$j] = true;
                    $frontier[] = $j;
                }
            }
        }

        for ($depth = 1; $depth <= self::LOOK_AROUND && $frontier; $depth++) {
            $next = [];
            foreach ($frontier as $j) {
                $total++;
                if ($class[$j] === self::BACKGROUND) {
                    $background++;
                } else {
                    if ($class[$j] === self::DARK) {
                        $darkRegions[$region[$j]] = true;
                    }
                    if ($depth > 2) {
                        $around[] = $luma[$j];
                    }
                }
                foreach (self::neighbours($j, $width, $count) as $k) {
                    if ($region[$k] !== $id && ! isset($seen[$k])) {
                        $seen[$k] = true;
                        $next[] = $k;
                    }
                }
            }
            $frontier = $next;
        }

        return [$total ? $background / $total : 1.0, $around, $darkRegions];
    }

    /**
     * Clears a region and fades the pixels around it by brightness, so its soft edge doesn't
     * leave a rim behind.
     *
     * @param  array<int, float>  $keep
     * @param  array<int, int>  $members
     * @param  array<int, int>  $region
     * @param  array<int, float>  $luma
     * @param  callable(float): float  $fade
     */
    private static function leaveOut(array &$keep, array $members, array $region, array $luma, int $width, int $count, int $depth, callable $fade): void
    {
        $id = $region[$members[0]];
        $seen = $frontier = [];

        foreach ($members as $i) {
            $keep[$i] = 0.0;
            foreach (self::neighbours($i, $width, $count) as $j) {
                if ($region[$j] !== $id && ! isset($seen[$j])) {
                    $seen[$j] = true;
                    $frontier[] = $j;
                }
            }
        }

        for ($step = 1; $frontier; $step++) {
            $next = [];
            foreach ($frontier as $j) {
                $keep[$j] = min($keep[$j], $fade($luma[$j]));
                if ($step === $depth) {
                    continue;
                }
                foreach (self::neighbours($j, $width, $count) as $k) {
                    if ($region[$k] !== $id && ! isset($seen[$k])) {
                        $seen[$k] = true;
                        $next[] = $k;
                    }
                }
            }
            $frontier = $next;
        }
    }

    /**
     * @return array<int, int>
     */
    private static function neighbours(int $i, int $width, int $count): array
    {
        $x = $i % $width;
        $neighbours = [];
        if ($x > 0) {
            $neighbours[] = $i - 1;
        }
        if ($x < $width - 1) {
            $neighbours[] = $i + 1;
        }
        if ($i >= $width) {
            $neighbours[] = $i - $width;
        }
        if ($i + $width < $count) {
            $neighbours[] = $i + $width;
        }

        return $neighbours;
    }

    private static function smoothstep(float $edge0, float $edge1, float $x): float
    {
        $t = max(0.0, min(1.0, ($x - $edge0) / ($edge1 - $edge0)));

        return $t * $t * (3 - 2 * $t);
    }
}
