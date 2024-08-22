<?php

namespace App\Http\Resources\api\Speakers;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SpecifiedSpeakersResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */

    /*    'name_en',
        'name_ar',
        'job_en',
        'job_ar',
        'country_en',
        'country_ar',
        'year',
        'desc_en',
        'desc_ar',
        'image',
    */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => [
                'en' => $this->name_en,
                'ar' => $this->name_ar,
            ],
            'job' => [
                'en' => $this->job_en,
                'ar' => $this->job_ar,
            ],
            'country' => [
                'en' => $this->country_en,
                'ar' => $this->country_ar,
            ],
            'year' => $this->year,
            'desc' => [
                'en' => $this->desc_en,
                'ar' => $this->desc_ar,
            ],
            'image' => $this->image,
        ];
    }
}
