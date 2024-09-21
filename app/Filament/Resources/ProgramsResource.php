<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProgramsResource\Pages;
use App\Filament\Resources\ProgramsResource\RelationManagers\SessionsProgramRelationManager;
use App\Models\Programs;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class ProgramsResource extends Resource
{
    protected static ?string $model = Programs::class;


    protected static ?string $navigationIcon = 'heroicon-o-tv';
    protected static ?string $navigationBadgeTooltip = 'عدد البرامج';

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('year')
                    //for the year select, from 2017 to the current year + 1 and make value selected is name of the year
                    ->options(array_combine(range(date('Y') + 1, 2017), range(date('Y') + 1, 2017)))->columnSpan('full'),
                Forms\Components\Toggle::make('day1')
                    ->required(),
                Forms\Components\Toggle::make('day2')
                    ->reactive()
                    ->afterStateUpdated(function (callable $set, $state) {
                        if ($state) {
                            $set('day1', true);
                        } else {
                            $set('day1', false);
                            $set('day2', false);
                            $set('day3', false);
                            $set('day4', false);
                        }
                    })
                    ->required(),
                Forms\Components\Toggle::make('day3')
                    ->reactive()
                    ->afterStateUpdated(function (callable $set, $state) {
                        if ($state) {
                            $set('day1', true);
                            $set('day2', true);


                        } else {
                            $set('day1', false);
                            $set('day2', false);
                            $set('day3', false);
                            $set('day4', false);
                        }
                    })
                    ->required(),
                Forms\Components\Toggle::make('day4')
                    ->reactive()
                    ->afterStateUpdated(function (callable $set, $state) {
                        if ($state) {
                            $set('day1', true);
                            $set('day2', true);
                            $set('day3', true);
                        } else {
                            $set('day1', false);
                            $set('day2', false);
                            $set('day3', false);
                            $set('day4', false);
                            $set('day5', false);
                        }
                    })
                    ->required(),
                Forms\Components\Toggle::make('day5')
                    ->reactive()
                    ->afterStateUpdated(function (callable $set, $state) {
                        if ($state && 'day1' && 'day2' && 'day3' && 'day4') {
                            $set('day1', true);
                            $set('day2', true);
                            $set('day3', true);
                            $set('day4', true);

                        } else {
                            $set('day1', false);
                            $set('day2', false);
                            $set('day3', false);
                            $set('day4', false);


                        }
                    })
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('year')
                    ->searchable(),


                Tables\Columns\IconColumn::make('day1')
                    ->boolean(),
                Tables\Columns\IconColumn::make('day2')
                    //if i day 2 is true change day 1 to tru
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
                Tables\Actions\EditAction::make()
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
