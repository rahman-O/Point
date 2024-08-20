<?php

namespace App\Filament\Resources\SessionsProgramResource\Pages;

use App\Filament\Resources\SessionsProgramResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewSessionsProgram extends ViewRecord
{
    protected static string $resource = SessionsProgramResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
