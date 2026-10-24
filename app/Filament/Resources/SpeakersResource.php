<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SpeakersResource\Pages;
use App\Models\Speakers;
use App\Models\SpeakerYear;
use Filament\Actions;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
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
                Forms\Components\Hidden::make('year')
                    ->default((string) date('Y')),
                Forms\Components\Select::make('years')
                    ->label('Years / السنوات')
                    ->helperText('A returning speaker keeps one profile: add every conference year here instead of creating a new copy.')
                    ->options(self::yearOptions())
                    ->multiple()
                    ->required()
                    ->live()
                    ->default([(string) date('Y')])
                    ->dehydrated(false)
                    ->afterStateHydrated(function (Forms\Components\Select $component, ?Speakers $record) {
                        if ($record) {
                            $component->state($record->yearList());
                        }
                    })
                    // The legacy single `year` column always holds the newest selected year.
                    ->afterStateUpdated(fn (Forms\Set $set, ?array $state) => $set('year', collect($state)->map(fn ($year) => (string) $year)->sort()->last()))
                    ->saveRelationshipsUsing(fn (Speakers $record, ?array $state) => $record->syncYears($state ?? [])),
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
                Tables\Columns\TextColumn::make('years.year')
                    ->label('Years')
                    ->badge(),
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
                Tables\Filters\SelectFilter::make('year')
                    ->label('Year')
                    ->options(self::yearOptions())
                    ->query(fn (Builder $query, array $data) => filled($data['value'] ?? null) ? $query->inYear($data['value']) : $query),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->action(function (Collection $records) {
                            [$linked, $free] = $records->partition(fn (Speakers $speaker) => $speaker->isUsedInAgenda());
                            $free->each->delete();

                            if ($linked->isEmpty()) {
                                Notification::make()->success()->title('Deleted')->send();

                                return;
                            }

                            Notification::make()
                                ->warning()
                                ->title("Deleted {$free->count()}, kept {$linked->count()} linked to agenda sessions")
                                ->body('Replace these speakers in their sessions first: ' . $linked->pluck('name_en')->implode(', '))
                                ->persistent()
                                ->send();
                        }),
                ]),
            ]);
    }

    public static function preventDeletingLinkedSpeaker(Actions\DeleteAction $action, Speakers $record): void
    {
        if (! $record->isUsedInAgenda()) {
            return;
        }

        Notification::make()
            ->danger()
            ->title('This speaker is linked to agenda sessions')
            ->body('Replace them with the main profile in those sessions (or remove them as facilitator) before deleting this copy.')
            ->persistent()
            ->send();

        $action->cancel();
    }

    private static function yearOptions(): array
    {
        $years = collect(range((int) date('Y') + 1, 2017))
            ->merge(SpeakerYear::query()->distinct()->pluck('year'))
            ->map(fn ($year) => (string) $year)
            ->unique()
            ->sortDesc()
            ->values();

        return $years->combine($years)->all();
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
