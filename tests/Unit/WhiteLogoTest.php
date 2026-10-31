<?php

use App\Images\WhiteLogo;

/**
 * Writes a PNG with filled rectangles ([r, g, b, x1, y1, x2, y2]) on a transparent or solid background.
 */
function drawLogo(int $width, int $height, ?array $background, array $rectangles): string
{
    $image = imagecreatetruecolor($width, $height);
    imagealphablending($image, false);
    imagesavealpha($image, true);
    imagefill($image, 0, 0, $background
        ? imagecolorallocate($image, ...$background)
        : imagecolorallocatealpha($image, 0, 0, 0, 127));

    foreach ($rectangles as [$r, $g, $b, $x1, $y1, $x2, $y2]) {
        imagefilledrectangle($image, $x1, $y1, $x2, $y2, imagecolorallocate($image, $r, $g, $b));
    }

    $path = sys_get_temp_dir() . '/logo-' . uniqid() . '.png';
    imagepng($image, $path);

    return $path;
}

/**
 * Renders the white copy of a logo file (deleting both files) and checks it only uses white.
 */
function whiteLogo(string $source): ?GdImage
{
    $target = sys_get_temp_dir() . '/white-logo-' . uniqid() . '.png';
    $size = WhiteLogo::render($source, $target);
    @unlink($source);

    if (! $size) {
        expect(file_exists($target))->toBeFalse();

        return null;
    }

    $image = imagecreatefrompng($target);
    unlink($target);

    $coloured = 0;
    for ($y = 0; $y < imagesy($image); $y++) {
        for ($x = 0; $x < imagesx($image); $x++) {
            $argb = imagecolorat($image, $x, $y);
            $coloured += (($argb >> 24) & 0x7F) < 127 && ($argb & 0xFFFFFF) !== 0xFFFFFF ? 1 : 0;
        }
    }
    expect([imagesx($image), imagesy($image)])->toBe($size)
        ->and($coloured)->toBe(0);

    return $image;
}

/**
 * [red, green, blue, alpha] at a relative position; alpha is 0 when opaque, 127 when transparent.
 */
function pixelAt(GdImage $image, float $x, float $y): array
{
    $argb = imagecolorat($image, (int) round((imagesx($image) - 1) * $x), (int) round((imagesy($image) - 1) * $y));

    return [($argb >> 16) & 0xFF, ($argb >> 8) & 0xFF, $argb & 0xFF, ($argb >> 24) & 0x7F];
}

it('paints a coloured logo white and crops its transparent margin', function () {
    $logo = whiteLogo(drawLogo(300, 200, null, [[200, 30, 30, 100, 50, 199, 149]]));

    expect(imagesx($logo) / imagesy($logo))->toEqualWithDelta(1, 0.05)
        ->and(pixelAt($logo, 0.5, 0.5))->toBe([255, 255, 255, 0]);
});

it('removes a solid background, including where it shows through the logo', function () {
    // A blue square with a white window, on a white (JPEG-like) background.
    $logo = whiteLogo(drawLogo(300, 200, [255, 255, 255], [
        [20, 60, 160, 100, 50, 199, 149],
        [255, 255, 255, 135, 85, 164, 114],
    ]));

    expect(imagesx($logo) / imagesy($logo))->toEqualWithDelta(1, 0.05)
        ->and(pixelAt($logo, 0.15, 0.5))->toBe([255, 255, 255, 0])
        ->and(pixelAt($logo, 0.5, 0.5)[3])->toBe(127);
});

it('cuts light details out of a coloured shape, even where they reach its edge', function () {
    // A white bar inside a green badge, running out to the badge's right edge.
    $logo = whiteLogo(drawLogo(300, 200, null, [
        [32, 176, 112, 50, 25, 249, 174],
        [255, 255, 255, 150, 85, 249, 114],
    ]));

    expect(imagesx($logo) / imagesy($logo))->toEqualWithDelta(200 / 150, 0.05)
        ->and(pixelAt($logo, 0.25, 0.5))->toBe([255, 255, 255, 0])
        ->and(pixelAt($logo, 0.8, 0.5)[3])->toBe(127);
});

it('drops a dark plate behind light lettering', function () {
    $logo = whiteLogo(drawLogo(300, 200, null, [
        [40, 40, 40, 50, 50, 249, 149],
        [255, 255, 255, 90, 85, 209, 114],
    ]));

    // Only the 120 x 30 light bar is left.
    expect(imagesx($logo) / imagesy($logo))->toEqualWithDelta(4, 0.3)
        ->and(pixelAt($logo, 0.5, 0.5))->toBe([255, 255, 255, 0]);
});

it('keeps a logo that is white already', function () {
    $logo = whiteLogo(drawLogo(300, 200, null, [[255, 255, 255, 100, 50, 249, 99]]));

    expect(imagesx($logo) / imagesy($logo))->toEqualWithDelta(3, 0.15)
        ->and(pixelAt($logo, 0.5, 0.5))->toBe([255, 255, 255, 0]);
});

it('gives up on unreadable files and images without a clear background', function () {
    $text = sys_get_temp_dir() . '/logo-' . uniqid() . '.png';
    file_put_contents($text, 'not an image');

    expect(whiteLogo($text))->toBeNull()
        ->and(whiteLogo(drawLogo(100, 100, null, [])))->toBeNull()
        // Four differently coloured quarters: there is no background colour to remove.
        ->and(whiteLogo(drawLogo(100, 100, [200, 0, 0], [
            [0, 200, 0, 50, 0, 99, 49],
            [0, 0, 200, 0, 50, 49, 99],
            [200, 200, 0, 50, 50, 99, 99],
        ])))->toBeNull();
});
