<?php

namespace App\Filament\Resources\ConferenceResource\Pages;

use App\Filament\Resources\ConferenceResource;
use App\Models\Conference;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListConferences extends ListRecords
{
    protected static string $resource = ConferenceResource::class;

    protected function getHeaderActions(): array
    {
        $lastRecord = Conference::latest('id')->first();
        return [
            Actions\CreateAction::make(),

//            Button::make('View Last Conference')
//                ->url($lastRecord ? $this->getResource()::getUrl('view', ['record' => $lastRecord->id]) : '#')
//                ->visible($lastRecord !== null),
        ];
    }
}
