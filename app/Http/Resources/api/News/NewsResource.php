<?php

namespace App\Http\Resources\api\News;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Resources\Json\ResourceCollection;

class NewsResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'title' => [
                'en' => $this->title_en,
                'ar' => $this->title_ar,
            ],
            'author' => [
                'en' => $this->author_en,
                'ar' => $this->author_ar,
            ],
            'desc' => [
                'en' => $this->desc_en,
                'ar' => $this->desc_ar,
            ],
            'image' => $this->image,
            'event_time' => $this->event_time,
        ];
    }
}
