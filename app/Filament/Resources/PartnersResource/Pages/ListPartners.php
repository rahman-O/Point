<?php

namespace App\Filament\Resources\PartnersResource\Pages;

use App\Enums\FooterSection;
use App\Filament\Concerns\TogglesFooterSection;
use App\Filament\Resources\PartnersResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListPartners extends ListRecords
{
    use TogglesFooterSection;

    protected static string $resource = PartnersResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ...$this->footerVisibilityActions(),
            Actions\CreateAction::make(),
        ];
    }

    protected function footerSection(): FooterSection
    {
        return FooterSection::Partners;
    }
}
