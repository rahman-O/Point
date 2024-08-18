<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProgramsResource\Pages;
use App\Filament\Resources\ProgramsResource\RelationManagers;
use App\Filament\Resources\ProgramsResource\RelationManagers\SessionsProgramRelationManager;
use App\Models\Programs;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ProgramsResource extends Resource
{
    protected static ?string $model = Programs::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->maxLength(255)
                    ->default(null),
                Forms\Components\TextInput::make('year')
                    ->required()
                    ->maxLength(255),
                Forms\Components\Toggle::make('day1')
                    ->required(),
                Forms\Components\Toggle::make('day2')
                    ->required(),
                Forms\Components\Toggle::make('day3')
                    ->required(),
                Forms\Components\Toggle::make('day4')
                    ->required(),
                Forms\Components\Toggle::make('day5')
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('year')
                    ->searchable(),
                Tables\Columns\IconColumn::make('day1')

                    ->boolean(),
                Tables\Columns\IconColumn::make('day2')
                    ->boolean(),
                Tables\Columns\IconColumn::make('day3')
                    ->boolean(),
                Tables\Columns\IconColumn::make('day4')
                    ->boolean(),
                Tables\Columns\IconColumn::make('day5')
                    ->boolean(),
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
            SessionsProgramRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPrograms::route('/'),
            'create' => Pages\CreatePrograms::route('/create'),
            'view' => Pages\ViewPrograms::route('/{record}'),
            'edit' => Pages\EditPrograms::route('/{record}/edit'),
        ];
    }
}
