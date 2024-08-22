<?php

namespace App\Http\Resources\api\Programs;

use App\Http\Resources\api\SessionsProgram\SessionsProgramResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Resources\Json\ResourceCollection;

class specifiedProgramResource extends JsonResource
{
    /**
     * Transform the resource collection into an array.
     *
     * @return array<int|string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'year' => $this->year,
            'day1' => $this->day1,
            'day2' => $this->day2,
            'day3' => $this->day3,
            'day4' => $this->day4,
            'day5' => $this->day5,
            // Uncomment and adjust the following if you have related sessionsProgram
             'sessionsProgram' => SessionsProgramResource::collection($this->sessionsProgram),
        ];
    }
}
