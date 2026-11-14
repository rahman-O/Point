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
                Forms\Components\TextInput::make('priority')
                    ->label('Position in speaker lists / الترتيب في قوائم المتحدثين')
                    ->helperText('1 shows first on the home page and the speakers page, then 2, 3… Speakers left at 0 come after the numbered ones, newest first / 1 يظهر أولاً في الصفحة الرئيسية وصفحة المتحدثين ثم 2 ثم 3… المتحدثون المتروكون على 0 يأتون بعد المرقّمين، الأحدث أولاً')
                    ->numeric()
                    ->integer()
                    ->minValue(0)
                    ->maxValue(999)
                    ->default(0)
                    ->required(),
                TinyEditor::make('desc_en')
                    ->required()
                    ->columnSpanFull(),

                TinyEditor::make('desc_ar')
                    ->required()
                    ->columnSpanFull(),
                // Every speaker photo is stored as an 800×1000 (4:5) portrait, so the site never has
                // to crop at display time. Any photo can be uploaded: it is cropped to 4:5 around its
                // centre automatically, and the editor lets admins move/zoom that crop before saving.
                FileUpload::make('image')
                    ->label('Photo / الصورة')
                    ->helperText('Any photo works; it is cropped to a 4:5 portrait automatically. Click the pencil to choose which part is kept. At least 400×500 px / أي صورة تصلح وتُقصّ تلقائياً إلى 4:5؛ اضغط القلم لاختيار الجزء المحفوظ. الحد الأدنى 400×500 بكسل')
                    ->image()
                    ->required()
                    ->imageEditor()
                    ->imageEditorAspectRatios(['4:5'])
                    ->imageCropAspectRatio('4:5')
                    ->imageResizeMode('cover')
                    ->imageResizeUpscale(false)
                    ->imageResizeTargetWidth('800')
                    ->imageResizeTargetHeight('1000')
                    ->rules(['dimensions:min_width=400,min_height=500'])
                    ->validationMessages([
                        'dimensions' => 'The photo is too small: it must be at least 400×500 px / الصورة صغيرة جداً: الحد الأدنى 400×500 بكسل',
                    ])
                    ->columnSpanFull(),
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
                Tables\Columns\TextColumn::make('priority')
                    ->label('Order / الترتيب')
                    ->tooltip('Position in the public speaker lists (1 first); 0 = default order')
                    ->sortable()
                    ->alignCenter()
                    ->formatStateUsing(fn (int $state) => $state === 0 ? '—' : (string) $state),
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
