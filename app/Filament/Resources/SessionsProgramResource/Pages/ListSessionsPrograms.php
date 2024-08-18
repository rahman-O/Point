<?php

namespace App\Filament\Resources\SessionsProgramResource\Pages;

use App\Filament\Resources\SessionsProgramResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListSessionsPrograms extends ListRecords
{
    protected static string $resource = SessionsProgramResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
