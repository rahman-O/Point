<?php

namespace App\Filament\Resources\SpeakersResource\Pages;

use App\Filament\Resources\SpeakersResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewSpeakers extends ViewRecord
{
    protected static string $resource = SpeakersResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
