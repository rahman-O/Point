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
                Forms\Components\RichEditor::make('desc_en')
                    ->required()
                    ->columnSpanFull(),

                Forms\Components\RichEditor::make('desc_ar')
                    ->required()
                    ->columnSpanFull(),
                    FileUpload::make('image')
                    ->image()
                    ->required()
                    ->afterStateUpdated(function ($state, callable $set) {
                        // Get the uploaded image's path
                        $imagePath = $state->getRealPath();

                        // Use Intervention Image to resize the image
                        $image = Image::make($imagePath);

                        // Resize to 600x400 pixels (3:2 aspect ratio)
                        $image->resize(600, 400, function ($constraint) {
                            $constraint->aspectRatio(); // Maintain aspect ratio
                            $constraint->upsize(); // Prevents upsizing of smaller images
                        });

                        // Save the resized image
                        $image->save($imagePath);

                        // Optionally, update the field's state (e.g., to a different path)
                        // $set('image', 'path/to/new-image.jpg');
                    }),
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
