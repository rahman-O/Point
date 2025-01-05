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
    /*['programs_id', 'name', 'day', 'year', 'start_time', 'end_time', 'subject_en', 'subject_ar', 'pres_en', 'pres_ar','facilitator_id']*/
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
                'en' => $this->subject_en,
                'ar' => $this->subject_ar,
            ],
            'pres' => [
                'en' => $this->pres_en,
                'ar' => $this->pres_ar,
            ],
            'facilitator_id' => new SpecifiedSpeakersResource($this->facilitator),
            'speakers' => $this->speakers,
            'title'=> [
                'en' => $this->title_en,
                'ar' => $this->title_ar,
            ],
       ];
    }
}
