<?php

namespace App\Filament\Resources\OrganizersResource\Pages;

use App\Enums\FooterSection;
use App\Filament\Concerns\TogglesFooterSection;
use App\Filament\Resources\OrganizersResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListOrganizers extends ListRecords
{
    use TogglesFooterSection;

    protected static string $resource = OrganizersResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ...$this->footerVisibilityActions(),
            Actions\CreateAction::make(),
        ];
    }

    protected function footerSection(): FooterSection
    {
        return FooterSection::Organizers;
    }
}
