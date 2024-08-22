<?php

namespace App\Http\Resources\api\Programs;

use App\Http\Resources\api\SessionsProgram\SessionsProgramResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class ProgramsResource extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @return array<int|string, mixed>
     */

    /*
     *  $table->id();
            $table->string('name')->nullable();
            $table->string('year');
            $table->boolean('day1')->default(false);
            $table->boolean('day2')->default(false);
            $table->boolean('day3')->default(false);
            $table->boolean('day4')->default(false);
            $table->boolean('day5')->default(false);*/
    public function toArray(Request $request): array
    {
        return
            $this->collection->map(function ($program) {
            return [
                'id' => $program->id,
                'name' => $program->name,
                'year' => $program->year,
                'day1' => $program->day1,
                'day2' => $program->day2,
                'day3' => $program->day3,
                'day4' => $program->day4,
                'day5' => $program->day5,
                // Uncomment and adjust the following if you have related sessionsProgram
                 'sessionsProgram' => SessionsProgramResource::collection($program->sessionsProgram),
            ];
        })->all();

    }
}
