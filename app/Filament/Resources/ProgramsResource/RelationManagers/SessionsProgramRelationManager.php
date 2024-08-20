<?php

namespace App\Filament\Resources\ProgramsResource\RelationManagers;

use App\Models\Programs;
use App\Models\Speakers;
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
                Forms\Components\TextInput::make('name')
                    ->maxLength(255),
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
                Forms\Components\RichEditor::make('sub_en')
                    ->columnSpan(2)
                    ->required()
                    ->maxLength(255),
                Forms\Components\RichEditor::make('sub_ar')
                    ->required()
                    ->columnSpan(2)
                    ->maxLength(255),
                Forms\Components\TextInput::make('pres_en')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('pres_ar')
                    ->required()
                    ->maxLength(255),
               Select::make('speakers')
                    ->relationship('speakers', 'name_en' )
                   ->multiple()
                    ->required(),


                Forms\Components\Select::make('facilitator_id')
                    ->relationship('speakers', 'name_en')
                    ->required()

            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->columns([
                Tables\Columns\TextColumn::make('name'),
                Tables\Columns\TextColumn::make('day'),


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
