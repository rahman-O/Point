<?php

namespace App\Filament\Resources\ProgramsResource\RelationManagers;

use App\Enums\SessionCategory;
use App\Filament\Resources\SessionsProgramResource\RelationManagers\SpeakerSessionsRelationManager;
use App\Models\Programs;
use App\Models\SearchEntry;
use App\Models\SessionsProgram;
use App\Models\Speakers;
use App\Search\TextNormalizer;
use Closure;
use Filament\Forms;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TimePicker;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Http;

class SessionsProgramRelationManager extends RelationManager
{
    protected static string $relationship = 'sessionsProgram';


    //id of the program
    public mixed $id;
    public mixed $year;

    public  function form(Form $form): Form
    {

        //get the program id from programs reourd id edition
       // $attributes = $form->model->getAttributes();
       $this->id =$this->getOwnerRecord()->getAttribute('id');
        $this-> year = $this->getOwnerRecord()->getAttribute('year');
        return $form
            ->schema([
                Forms\Components\TextInput::make('programs_id')
                    //get the program id from programs reourd id edition

                    ->default($this->id)
                    ->disabled()
                    ->required(),
                Forms\Components\TextInput::make('year')
                    ->default($this->year)
                    ->readOnly()
                    ->required(),
                Forms\Components\Select::make('day')
                    //get the day select options from porgams table where day1, day2, day3, day4, day5 == true
                    ->options($this->getOption())
                    ->required(),
                Forms\Components\Select::make('category')
                    ->label('Type / النوع')
                    ->helperText('Decides which section of the public agenda this item appears in / يحدد القسم الذي يظهر فيه هذا البند في جدول الأعمال')
                    ->options(SessionCategory::options())
                    ->default(SessionCategory::DEFAULT->value)
                    ->required()
                    ->native(false),
                Forms\Components\TextInput::make('priority')
                    ->label('Order among parallel items / الترتيب بين البنود المتزامنة')
                    ->helperText('Only matters when several items share the same time: 1 shows first, then 2, 3… Items left at 0 come after the numbered ones, in their usual order / يُستخدم فقط عندما تتزامن عدة بنود في نفس الوقت: 1 يظهر أولاً ثم 2 ثم 3… البنود المتروكة على 0 تأتي بعد المرقّمة بترتيبها المعتاد')
                    ->numeric()
                    ->integer()
                    ->minValue(0)
                    ->maxValue(999)
                    ->default(0)
                    ->required(),

                Forms\Components\TimePicker::make('start_time')
                    //remove seconds from the time picker
                        ->seconds(false)
                    ->required(),
                Forms\Components\TimePicker::make('end_time')
                    ->seconds(false)
                    ->required(),
                    Forms\Components\TextInput::make('title_en')
                ->maxLength(255),
                Forms\Components\TextInput::make('title_ar')
                ->maxLength(255),
                Forms\Components\RichEditor::make('subject_en')
                    ->columnSpan(2)
                    ->required()
                    ->maxLength(255),
                Forms\Components\RichEditor::make('subject_ar')
                    ->required()
                    ->columnSpan(2)
                    ->maxLength(255),
                Forms\Components\TextInput::make('presentation_en')
                    ->maxLength(255),
                Forms\Components\TextInput::make('presentation_ar')
                    ->maxLength(255),
                // Who appears under the item on the public agenda: either speakers with their
                // profiles (and optional roles), or plain participants (name, photo, optional
                // description) that are not linked to a speaker page.
                Forms\Components\Radio::make('people_mode')
                    ->label('People shown on the agenda / الأشخاص الظاهرون في جدول الأعمال')
                    ->options([
                        'speakers' => 'Speakers & roles (linked to speaker profiles) / متحدثون وأدوارهم (مرتبطون بصفحات المتحدثين)',
                        'participants' => 'Custom participants (name, photo, description; not clickable) / مشاركون مخصصون (اسم، صورة، شرح؛ غير قابلين للفتح)',
                    ])
                    ->default('speakers')
                    ->live()
                    ->dehydrated(false)
                    ->afterStateHydrated(fn (Forms\Components\Radio $component, ?SessionsProgram $record) => $component->state(filled($record?->participants) ? 'participants' : 'speakers'))
                    ->columnSpan(2),

                Forms\Components\Group::make([
                    Select::make('speakers')
                        ->label('Speakers')
                        ->relationship('speakers', 'name_en')
                        ->multiple()
                        ->searchable()
                        ->live()
                        ->afterStateUpdated(function ($state, Forms\Set $set, Forms\Get $get) {
                            // Drop a role if that speaker was removed from the session.
                            foreach (SessionsProgram::ROLE_COLUMNS as $role) {
                                if (! in_array($get($role), (array) $state)) {
                                    $set($role, null);
                                }
                            }
                            $set('mcs', array_values(array_intersect((array) $get('mcs'), (array) $state)));
                        })
                        ->getSearchResultsUsing(function (string $search) {
                            $year = $this->year ?? now()->year;

                            return Speakers::inYear($year)
                                ->where(function($query) use ($search) {
                                    $query->where('name_en', 'like', "%{$search}%")
                                          ->orWhere('name_ar', 'like', "%{$search}%");
                                })
                                ->limit(20)
                                ->get(['id', 'name_en', 'name_ar'])
                                ->mapWithKeys(fn (Speakers $speaker) => [$speaker->id => self::bilingualName($speaker)])
                                ->toArray();
                        })
                        ->getOptionLabelFromRecordUsing(fn (Speakers $speaker) => self::bilingualName($speaker)),
                    // Optional roles, each picked from the speakers chosen above. On the public
                    // agenda role holders are listed first, each with its own coloured badge.
                    $this->roleSelect('facilitator_id', 'Facilitator / ميسر', 'ميسر الجلسة / Facilitator'),
                    $this->roleSelect('rapporteur_id', 'Rapporteur / مقرر', 'مقرر الجلسة / Rapporteur'),
                    $this->roleSelect('trainer_id', 'Trainer / مدرب', 'مدرب / Trainer'),
                    $this->roleSelect('chair_id', 'Session chair / إدارة الجلسة', 'إدارة الجلسة / Chair'),
                    $this->roleSelect('lecturer_id', 'Lecturer / محاضر', 'محاضر / Lecturer'),
                    $this->mcSelect(),
                ])
                    ->columns(2)
                    ->columnSpan(2)
                    ->visible(fn (Forms\Get $get) => $get('people_mode') === 'speakers'),

                Forms\Components\Repeater::make('participants')
                    ->label('Participants / المشاركون')
                    ->helperText('Shown on the agenda exactly like speakers, in this order, but without a link to a speaker page / يظهرون في جدول الأعمال مثل المتحدثين وبهذا الترتيب، بدون رابط لصفحة متحدث')
                    ->schema([
                        Forms\Components\TextInput::make('name_en')
                            ->label('Name (EN) / الاسم بالإنجليزية')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('name_ar')
                            ->label('Name (AR) / الاسم بالعربية')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('desc_en')
                            ->label('Description (EN) / الشرح بالإنجليزية')
                            ->maxLength(255),
                        Forms\Components\TextInput::make('desc_ar')
                            ->label('Description (AR) / الشرح بالعربية')
                            ->maxLength(255),
                        Forms\Components\FileUpload::make('image')
                            ->label('Photo / الصورة')
                            ->image()
                            ->required()
                            ->imageEditor()
                            ->imageEditorAspectRatios(['1:1'])
                            ->imageCropAspectRatio('1:1')
                            ->imageResizeMode('cover')
                            ->imageResizeUpscale(false)
                            ->imageResizeTargetWidth('400')
                            ->imageResizeTargetHeight('400')
                            ->columnSpan(2),
                    ])
                    ->columns(2)
                    ->itemLabel(fn (array $state): ?string => $state['name_ar'] ?? $state['name_en'] ?? null)
                    ->addActionLabel('Add participant / إضافة مشارك')
                    ->reorderable()
                    ->collapsible()
                    ->defaultItems(1)
                    ->minItems(1)
                    ->columnSpan(2)
                    ->visible(fn (Forms\Get $get) => $get('people_mode') === 'participants'),
            ]);
    }

    /**
     * Keeps the two ways of listing people exclusive: with custom participants the speaker
     * roles are cleared; with speakers the participants are dropped. Hidden fields are not
     * submitted by Filament, so this is done from the submitted radio state.
     */
    private function applyPeopleMode(array $data): array
    {
        $mode = $this->mountedTableActionsData[0]['people_mode'] ?? 'speakers';

        if ($mode === 'participants') {
            $data['participants'] = array_values(array_filter(
                (array) ($data['participants'] ?? []),
                fn ($p) => is_array($p) && filled($p['name_en'] ?? null) && filled($p['image'] ?? null),
            )) ?: null;
            foreach (SessionsProgram::ROLE_COLUMNS as $role) {
                $data[$role] = null;
            }
        } else {
            $data['participants'] = null;
        }

        return $data;
    }

    private function detachSpeakersIfParticipants(SessionsProgram $record): void
    {
        if (filled($record->participants)) {
            $record->speakers()->detach();
            $record->mcs()->detach();
        }
    }

    /**
     * Unlike the other roles, the master of ceremonies (العريف) can be held by several of the
     * session's speakers, so it is a multi-select saved through the mcs() relation.
     */
    private function mcSelect(): Select
    {
        $fromChosenSpeakers = fn (Forms\Get $get, ?string $search = null) => Speakers::whereIn('id', array_filter((array) $get('speakers')))
            ->when($search, fn (Builder $q) => $q->where(fn (Builder $q) => $q
                ->where('name_en', 'like', "%{$search}%")
                ->orWhere('name_ar', 'like', "%{$search}%")))
            ->get(['id', 'name_en', 'name_ar'])
            ->mapWithKeys(fn (Speakers $speaker) => [$speaker->id => self::bilingualName($speaker)])
            ->toArray();

        return Select::make('mcs')
            ->label('Masters of ceremonies / العريف')
            ->helperText('Choose one or more of the session speakers above as masters of ceremonies (add them to Speakers first). On the public agenda they are listed first with a "العريف / MC" badge.')
            ->placeholder('None / لا يوجد')
            ->relationship('mcs', 'name_en')
            ->multiple()
            ->options($fromChosenSpeakers)
            ->getSearchResultsUsing(fn (string $search, Forms\Get $get) => $fromChosenSpeakers($get, $search))
            ->getOptionLabelFromRecordUsing(fn (Speakers $speaker) => self::bilingualName($speaker))
            ->searchable();
    }

    private function roleSelect(string $column, string $label, string $badge): Select
    {
        return Select::make($column)
            ->label($label)
            ->helperText("Choose which of the session speakers above holds this role (add them to Speakers first). On the public agenda they are listed first with a \"{$badge}\" badge.")
            ->placeholder('None / لا يوجد')
            ->options(fn (Forms\Get $get) => Speakers::whereIn('id', array_filter((array) $get('speakers')))
                ->get(['id', 'name_en', 'name_ar'])
                ->mapWithKeys(fn (Speakers $speaker) => [$speaker->id => self::bilingualName($speaker)])
                ->toArray())
            ->searchable()
            ->nullable();
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('presentation_ar')
            ->columns([
                Tables\Columns\TextColumn::make('year'),
                Tables\Columns\TextColumn::make('day')
                    ->formatStateUsing(fn ($state) => match ($state) {
                        'day1' => 'Day 1',
                        'day2' => 'Day 2',
                        'day3' => 'Day 3',
                        'day4' => 'Day 4',
                        'day5' => 'Day 5',
                    }),
                //format value day1 to Day
                Tables\Columns\TextColumn::make('category')
                    ->label('Type / النوع')
                    ->badge()
                    ->formatStateUsing(fn (SessionCategory $state) => $state->label())
                    ->color(fn (SessionCategory $state) => match ($state) {
                        SessionCategory::Sessions => 'primary',
                        SessionCategory::Workshops => 'success',
                        SessionCategory::Meetings => 'warning',
                        SessionCategory::Forums => 'info',
                    }),
                Tables\Columns\TextColumn::make('start_time')
                    ->date('H:i'),
                Tables\Columns\TextColumn::make('end_time')
                    ->date('H:i'),
                Tables\Columns\TextColumn::make('priority')
                    ->label('Order / الترتيب')
                    ->tooltip('Order among items sharing the same time (1 first); 0 = default')
                    ->sortable()
                    ->alignCenter()
                    ->formatStateUsing(fn (int $state) => $state === 0 ? '—' : (string) $state),
                Tables\Columns\TextColumn::make('title_en')
                    ->label('Title')
                    ->description(fn ($record) => $record->title_ar)
                    ->grow()
                    ->wrap()
                    ->searchable(query: function (Builder $query, string $search): Builder {
                        // The site search index stores normalized Arabic/English text (hamza, taa marbuta, diacritics…).
                        $normalized = TextNormalizer::normalize($search);

                        return $query->where(function (Builder $query) use ($search, $normalized) {
                            $query->where('title_en', 'like', "%{$search}%")
                                ->orWhere('title_ar', 'like', "%{$search}%");

                            if ($normalized !== '') {
                                $query->orWhereIn('id', SearchEntry::query()
                                    ->where('entry_type', 'session')
                                    ->where('search_all', 'like', "%{$normalized}%")
                                    ->select('entry_id'));
                            }
                        });
                    })
                    ->placeholder('—'),
                    Tables\Columns\TextColumn::make('speakers.name_en')
                        ->label('Speakers')
                        ->width('14rem')
                        ->wrap()
                        ->searchable(['name_en', 'name_ar']),
                Tables\Columns\TextColumn::make('facilitator.name_en')
                    ->label('Facilitator / ميسر')
                    ->width('10rem')
                    ->wrap()
                    ->searchable(['name_en', 'name_ar'])
                    ->placeholder('—'),
                Tables\Columns\TextColumn::make('rapporteur.name_en')
                    ->label('Rapporteur / مقرر')
                    ->width('10rem')
                    ->wrap()
                    ->searchable(['name_en', 'name_ar'])
                    ->placeholder('—')
                    ->toggleable(),
                Tables\Columns\TextColumn::make('trainer.name_en')
                    ->label('Trainer / مدرب')
                    ->width('10rem')
                    ->wrap()
                    ->searchable(['name_en', 'name_ar'])
                    ->placeholder('—')
                    ->toggleable(),
                Tables\Columns\TextColumn::make('chair.name_en')
                    ->label('Chair / إدارة الجلسة')
                    ->width('10rem')
                    ->wrap()
                    ->searchable(['name_en', 'name_ar'])
                    ->placeholder('—')
                    ->toggleable(),
                Tables\Columns\TextColumn::make('lecturer.name_en')
                    ->label('Lecturer / محاضر')
                    ->width('10rem')
                    ->wrap()
                    ->searchable(['name_en', 'name_ar'])
                    ->placeholder('—')
                    ->toggleable(),
                Tables\Columns\TextColumn::make('mcs.name_en')
                    ->label('MC / العريف')
                    ->listWithLineBreaks()
                    ->width('10rem')
                    ->wrap()
                    ->searchable(['name_en', 'name_ar'])
                    ->placeholder('—')
                    ->toggleable(),

            ])
            ->filters([
                Tables\Filters\SelectFilter::make('day')
                    ->label('Day / اليوم')
                    // Only the days switched on for this program, like the form's day select.
                    ->options(fn () => $this->getOption()),
                Tables\Filters\SelectFilter::make('category')
                    ->label('Type / النوع')
                    ->options(SessionCategory::options()),
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->mutateFormDataUsing(fn (array $data) => $this->applyPeopleMode($data))
                    ->after(fn (SessionsProgram $record) => $this->detachSpeakersIfParticipants($record)),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->mutateFormDataUsing(fn (array $data) => $this->applyPeopleMode($data))
                    ->after(fn (SessionsProgram $record) => $this->detachSpeakersIfParticipants($record)),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }


    private static function bilingualName(Speakers $speaker): string
    {
        return collect([trim((string) $speaker->name_en), trim((string) $speaker->name_ar)])
            ->filter()
            ->unique()
            ->implode(' — ');
    }

    function getOption():array
    {
        // The table filter can run before form() sets $this->id, so read the owner directly.
        $programs = Programs::all()->where('id', $this->id ?? $this->getOwnerRecord()->getKey());

// Initialize an empty array to store the select options
        $options = [];

// Loop through each program to check the boolean fields
        foreach ($programs as $program) {
            if ($program->day1==1) {
                $options['day1'] = 'Day 1';
            }
            if ($program->day2==1) {
                $options['day2'] = 'Day 2';
            }
            if ($program->day3==1) {
                $options['day3'] = 'Day 3';
            }
            if ($program->day4) {
                $options['day4'] = 'Day 4';
            }
            if ($program->day5) {
                $options['day5'] = 'Day 5';
            }
        }

// Remove any empty values
        $options = array_filter($options);

        return $options;
    }
}
