<?php

namespace App\Filament\Resources\SessionsProgramResource\Pages;

use App\Filament\Resources\SessionsProgramResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditSessionsProgram extends EditRecord
{
    protected static string $resource = SessionsProgramResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }
}
