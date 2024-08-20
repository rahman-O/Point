<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SessionsProgramResource\Pages;
use App\Filament\Resources\SessionsProgramResource\RelationManagers;
use App\Models\SessionsProgram;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class SessionsProgramResource extends Resource
{

    protected static ?string $model = SessionsProgram::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {

       // dd($attributes);
        return $form
            ->schema([
                Forms\Components\TextInput::make('programs_id')
                    ->required()

                    ->numeric(),
                Forms\Components\TextInput::make('name')
                    ->maxLength(255)
                    ->default(null),
                Forms\Components\Select::make('day')
                    //get the day select options from porgams table

                    ->options([
                        'day1' => 'Day 1',
                        'day2' => 'Day 2',
                        'day3' => 'Day 3',
                        'day4' => 'Day 4',
                        'day5' => 'Day 5',
                    ])
                    ->required(),
                Forms\Components\TextInput::make('year')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('start_time')
                    ->required(),
                Forms\Components\TextInput::make('end_time')
                    ->required(),
                Forms\Components\TextInput::make('sub_en')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('sub_ar')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('pres_en')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('pres_ar')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('speaker_id')
                    ->required()
                    ->numeric(),
                Forms\Components\TextInput::make('facilitator_id')
                    ->required()
                    ->numeric(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('program_id')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('day')
                    ->searchable(),
                Tables\Columns\TextColumn::make('year')
                    ->searchable(),
                Tables\Columns\TextColumn::make('start_time'),
                Tables\Columns\TextColumn::make('end_time'),
                Tables\Columns\TextColumn::make('sub_en')
                    ->searchable(),
                Tables\Columns\TextColumn::make('sub_ar')
                    ->searchable(),
                Tables\Columns\TextColumn::make('pres_en')
                    ->searchable(),
                Tables\Columns\TextColumn::make('pres_ar')
                    ->searchable(),
                Tables\Columns\TextColumn::make('speaker_id')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('facilitator_id')
                    ->numeric()
                    ->sortable(),
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
            'index' => Pages\ListSessionsPrograms::route('/'),
            'create' => Pages\CreateSessionsProgram::route('/create'),
            'view' => Pages\ViewSessionsProgram::route('/{record}'),
            'edit' => Pages\EditSessionsProgram::route('/{record}/edit'),
        ];
    }
}
