<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SpeakersResource\Pages;
use App\Models\Speakers;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Intervention\Image\Facades\Image;
use Filament\Forms\Components\FileUpload;
use Mohamedsabil83\FilamentFormsTinyeditor\Components\TinyEditor;

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
                Forms\Components\Select::make('year')
                    ->options(array_combine(range(date('Y') + 1, 2017), range(date('Y') + 1, 2017)))
                    ->required(),
                TinyEditor::make('desc_en')
                    ->required()
                    ->columnSpanFull(),

                TinyEditor::make('desc_ar')
                    ->required()
                    ->columnSpanFull(),
                FileUpload::make('image')
                    ->image()
                    ->required()
                    ->imageEditor()
                    ->imageEditorAspectRatios([
                        null,
                        '16:9',
                        '4:3',
                        '3:2',
                        '1:1',
                    ])
                    ->imageEditorMode(2)
                    ->imageEditorEmptyFillColor('#000000')
                    ->imageResizeMode('cover')
                    ->imageResizeTargetWidth('600')
                    ->imageResizeTargetHeight('400'),
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
                Tables\Columns\TextColumn::make('year'),
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
