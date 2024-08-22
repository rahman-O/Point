<?php

namespace App\Http\Resources\api\SessionsProgram;

use App\Http\Resources\api\Speakers\SpecifiedSpeakersResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Resources\Json\ResourceCollection;

class SessionsProgramResource extends JsonResource
{
    /**
     * Transform the resource collection into an array.
     *
     * @return array<int|string, mixed>
     */
    /*['programs_id', 'name', 'day', 'year', 'start_time', 'end_time', 'sub_en', 'sub_ar', 'pres_en', 'pres_ar','facilitator_id']*/
    public function toArray(Request $request): array
    {
       return [
            'id' => $this->id,
            'programs_id' => $this->programs_id,
            'name' => $this->name,
            'day' => $this->day,
            'year' => $this->year,
            'start_time' => $this->start_time,
            'end_time' => $this->end_time,
            'sub'=> [
                'en' => $this->sub_en,
                'ar' => $this->sub_ar,
            ],
            'pres' => [
                'en' => $this->pres_en,
                'ar' => $this->pres_ar,
            ],
            'facilitator_id' => new SpecifiedSpeakersResource($this->facilitator),
            'speakers' => $this->speakers,
       ];
    }
}
