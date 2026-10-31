<?php

namespace App\Http\Controllers;

use App\Images\FooterLogos;

/**
 * Serves the white copy of an organizer/partner logo shown in the site footer.
 */
class FooterLogoController extends Controller
{
    public function __invoke(string $image)
    {
        $path = FooterLogos::path($image);
        abort_unless($path, 404);

        return response()->file($path, [
            'Content-Type' => 'image/png',
            'Cache-Control' => 'public, max-age=604800',
        ]);
    }
}
