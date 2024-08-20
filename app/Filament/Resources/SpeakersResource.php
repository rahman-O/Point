<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProgramsResource\RelationManagers\SessionsProgramRelationManager;
use App\Filament\Resources\SpeakersResource\Pages;
use App\Filament\Resources\SpeakersResource\RelationManagers;
use App\Filament\Resources\SpeakersResource\RelationManagers\SessionProgramsRelationManager;
use App\Filament\Resources\SpeakersResource\RelationManagers\SessionsspeakerssRelationManager;
use App\Models\Speakers;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class SpeakersResource extends Resource
{
    protected static ?string $model = Speakers::class;
    protected static string $relationship = 'sessions';
    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name_en')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('name_ar')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('job_en')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('job_ar')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('country_en')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('country_ar')
                    ->required()
                    ->maxLength(255),
                Forms\Components\Select::make('year')
                    ->required()
                    //for the year select, from 2017 to the current year + 1 and make value selected is name of the year
                    ->options( array_combine(range(date('Y') + 1, 2017), range(date('Y') + 1, 2017))),

                Forms\Components\RichEditor::make('desc_en')

                    ->required()
                    ->columnSpanFull(),
                Forms\Components\RichEditor::make('desc_ar')
                    ->required()
                    ->columnSpanFull(),
                Forms\Components\FileUpload::make('image')
                    ->image()
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name_en')
                    ->searchable(),
                Tables\Columns\TextColumn::make('name_ar')
                    ->searchable(),
                Tables\Columns\TextColumn::make('job_en')
                    ->searchable(),
                Tables\Columns\TextColumn::make('job_ar')
                    ->searchable(),
                Tables\Columns\TextColumn::make('country_en')
                    ->searchable(),
                Tables\Columns\TextColumn::make('country_ar')
                    ->searchable(),

                Tables\Columns\TextColumn::make('year')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\ImageColumn::make('image'),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),


            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            SessionProgramsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSpeakers::route('/'),
            'create' => Pages\CreateSpeakers::route('/create'),
            'view' => Pages\ViewSpeakers::route('/{record}'),
            'edit' => Pages\EditSpeakers::route('/{record}/edit'),
        ];
    }
}
