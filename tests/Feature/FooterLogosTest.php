<?php

use App\Models\Organizers;
use App\Models\Partners;
use Illuminate\Support\Facades\File;

// Points storage_path() at a throwaway folder holding one partner logo (red on white, 5:1).
function useLogoStorage(): string
{
    $storage = sys_get_temp_dir() . '/point-footer-logos-' . uniqid();
    File::ensureDirectoryExists($storage . '/app/public');

    $image = imagecreatetruecolor(240, 80);
    imagefill($image, 0, 0, imagecolorallocate($image, 255, 255, 255));
    imagefilledrectangle($image, 20, 20, 219, 59, imagecolorallocate($image, 200, 30, 30));
    imagejpeg($image, $storage . '/app/public/partner.jpg');

    app()->useStoragePath($storage);

    return $storage;
}

afterEach(function () {
    foreach (glob(sys_get_temp_dir() . '/point-footer-logos-*') ?: [] as $directory) {
        File::deleteDirectory($directory);
    }
});

it('serves a white copy of each footer logo and leaves the original untouched', function () {
    $storage = useLogoStorage();
    $original = md5_file($storage . '/app/public/partner.jpg');
    Partners::create(['name' => 'Partner', 'image' => 'partner.jpg']);
    Organizers::create(['name' => 'No file', 'image' => 'missing.png']);

    $logo = $this->getJson('/api/footer')
        ->assertOk()
        ->assertJsonPath('organizers.items.0.logo', null)
        ->json('partners.items.0.logo');

    expect($logo['src'])->toStartWith('/api/footer-logos/partner.jpg?v=')
        ->and($logo['width'] / $logo['height'])->toEqualWithDelta(5, 0.3);

    $this->get($logo['src'])->assertOk()->assertHeader('Content-Type', 'image/png');

    expect(md5_file($storage . '/app/public/partner.jpg'))->toBe($original);
});

it('does not serve unknown or unsafe logo names', function () {
    useLogoStorage();

    $this->get('/api/footer-logos/missing.png')->assertNotFound();
    $this->get('/api/footer-logos/.env')->assertNotFound();
});
