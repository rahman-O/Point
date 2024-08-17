<?php

namespace App\Filament\Resources\SpeakersResource\Pages;

use App\Filament\Resources\SpeakersResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListSpeakers extends ListRecords
{
    protected static string $resource = SpeakersResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
