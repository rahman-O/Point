<?php

namespace App\Filament\Resources\SpeakersResource\Pages;

use App\Filament\Resources\SpeakersResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditSpeakers extends EditRecord
{
    protected static string $resource = SpeakersResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }
}
