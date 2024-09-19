<?php

namespace App\Filament\Resources\OrganizersResource\Pages;

use App\Filament\Resources\OrganizersResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditOrganizers extends EditRecord
{
    protected static string $resource = OrganizersResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }
}
