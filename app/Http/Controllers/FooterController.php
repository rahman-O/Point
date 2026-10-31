<?php

namespace App\Http\Controllers;

use App\Enums\FooterSection;
use App\Images\FooterLogos;
use App\Models\Organizers;
use App\Models\Partners;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\JsonResponse;

/**
 * Logo sections of the public site footer. Admins can hide a section from its list page;
 * hidden sections are returned without items. Each logo comes with a white copy for the
 * dark footer (null when the image can't be converted).
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
            'items' => $visible
                ? $model::query()->get(['id', 'name', 'image'])->map(fn (Model $item): array => [
                    ...$item->toArray(),
                    'logo' => FooterLogos::for($item->image),
                ])
                : [],
        ];
    }
}
