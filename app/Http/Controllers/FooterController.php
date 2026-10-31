<?php

namespace App\Http\Controllers;

use App\Enums\FooterSection;
use App\Models\Organizers;
use App\Models\Partners;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\JsonResponse;

/**
 * Logo sections of the public site footer. Admins can hide a section from its list page;
 * hidden sections are returned without items.
 */
class FooterController extends Controller
{
    public function __invoke(): JsonResponse
    {
        return response()->json([
            'organizers' => $this->section(FooterSection::Organizers, Organizers::class),
            'partners' => $this->section(FooterSection::Partners, Partners::class),
        ]);
    }

    /**
     * @param  class-string<Model>  $model
     */
    private function section(FooterSection $section, string $model): array
    {
        $visible = $section->isVisible();

        return [
            'visible' => $visible,
            'items' => $visible ? $model::query()->get(['id', 'name', 'image']) : [],
        ];
    }
}
