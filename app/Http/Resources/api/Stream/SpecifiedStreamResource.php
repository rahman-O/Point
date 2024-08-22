<?php

namespace App\Http\Resources\api\Stream;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SpecifiedStreamResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    /*
    protected $fillable = [
        'title_en',
        'title_ar',
        'url',
        'youtube_video_id',
        'date',
    ];     * */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => [
                'en' => $this->title_en,
                'ar' => $this->title_ar,
            ],
            'url' => $this->url,
            'youtube_video_id' => $this->youtube_video_id,
            'date' => $this->date,
            'youtube_thumbnail_url' => $this->youtube_thumbnail_url,
        ];
    }
}
