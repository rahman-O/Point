<?php

namespace App\Filament\Resources\ProgramsResource\Pages;

use App\Filament\Resources\ProgramsResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewPrograms extends ViewRecord
{
    protected static string $resource = ProgramsResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
