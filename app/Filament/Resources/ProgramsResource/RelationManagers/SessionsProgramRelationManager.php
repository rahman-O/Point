<?php

namespace App\Filament\Resources\ProgramsResource\RelationManagers;

use App\Filament\Resources\SessionsProgramResource\RelationManagers\SpeakerSessionsRelationManager;
use App\Models\Programs;
use App\Models\SearchEntry;
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
                    Select::make('speakers')
                        ->label('Speakers')
                        ->relationship('speakers', 'name_en')
                        ->multiple()
                        ->searchable()
                        ->live()
                        ->afterStateUpdated(function ($state, Forms\Set $set, Forms\Get $get) {
                            // Drop the facilitator if that speaker was removed from the session.
                            if (! in_array($get('facilitator_id'), (array) $state)) {
                                $set('facilitator_id', null);
                            }
                        })
                        ->getSearchResultsUsing(function (string $search) {
                            $year = $this->year ?? now()->year;

                            return Speakers::where('year', $year)
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
                    Select::make('facilitator_id')
                        ->label('Facilitator / ميسر')
                        ->helperText('Choose which of the session speakers above is the facilitator (add them to Speakers first). On the public agenda they are listed first with a "ميسر الجلسة / Facilitator" badge.')
                        ->placeholder('No facilitator')
                        ->options(fn (Forms\Get $get) => Speakers::whereIn('id', array_filter((array) $get('speakers')))
                            ->get(['id', 'name_en', 'name_ar'])
                            ->mapWithKeys(fn (Speakers $speaker) => [$speaker->id => self::bilingualName($speaker)])
                            ->toArray())
                        ->searchable()
                        ->nullable(),
            ]);
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
                Tables\Columns\TextColumn::make('start_time')
                    ->date('H:i'),
                Tables\Columns\TextColumn::make('end_time')
                    ->date('H:i'),
                Tables\Columns\TextColumn::make('title_en')
                    ->label('Title')
                    ->description(fn ($record) => $record->title_ar)
                    ->wrap()
                    ->limit(60)
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
                        ->searchable(['name_en', 'name_ar']),
                Tables\Columns\TextColumn::make('facilitator.name_en')
                    ->label('Facilitator / ميسر')
                    ->searchable(['name_en', 'name_ar'])
                    ->placeholder('—'),

            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
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
        // Retrieve all programs
        $programs = Programs::all()->where('id', $this->id);

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
