<?php

namespace App\Http\Controllers;

use App\Enums\FooterSection;
use App\Images\FooterLogos;
use App\Models\Organizers;
use App\Models\Partners;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\JsonResponse;

/**
 * Logo sections of the public site footer. Admins can hide a section from its list page;
 * hidden sections are returned without items. Partners come in the order admins arranged
 * them. Each logo comes with a white copy for the dark footer (null when the image can't
 * be converted).
 */
class FooterController extends Controller
{
    public function __invoke(): JsonResponse
    {
        return response()->json([
            'organizers' => $this->section(FooterSection::Organizers, Organizers::query()),
            'partners' => $this->section(FooterSection::Partners, Partners::ordered()),
        ]);
    }

    private function section(FooterSection $section, Builder $items): array
    {
        $visible = $section->isVisible();

        return [
            'visible' => $visible,
            'items' => $visible
                ? $items->get(['id', 'name', 'image'])->map(fn (Model $item): array => [
                    ...$item->toArray(),
                    'logo' => FooterLogos::for($item->image),
                ])
                : [],
        ];
    }
}
