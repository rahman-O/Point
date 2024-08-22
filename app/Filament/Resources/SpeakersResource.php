<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SpeakersResource\Pages;
use App\Models\Speakers;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class SpeakersResource extends Resource
{
    protected static ?string $model = Speakers::class;
    protected static string $relationship = 'sessions';
    protected static ?string $navigationIcon = 'heroicon-o-queue-list';
    protected static ?string $navigationBadgeTooltip = 'عدد الاجندات';

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }

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
                Forms\Components\DatePicker::make('year')// Optionally set the current year as the default
                ->required(),
                Forms\Components\Textarea::make('desc_en')
                    ->required()
                    ->columnSpanFull(),
                Forms\Components\Textarea::make('desc_ar')
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
            //
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
