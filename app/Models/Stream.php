<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Stream extends Model
{
    use HasFactory;

    protected $fillable = [
        'title_en',
        'title_ar',
        'url',
        'youtube_video_id',
        'date',
    ];

    public static function boot()
    {
        parent::boot();

        static::saving(function ($model) {
            $model->youtube_video_id = self::extractYoutubeId($model->url);
        });
    }

    public static function extractYoutubeId($url)
    {
        preg_match('/(?:https?:\/\/)?(?:www\.)?(?:youtube\.com\/watch\?v=|youtu\.be\/)([a-zA-Z0-9_-]+)/', $url, $matches);
        return $matches[1] ?? null;
    }

    public function getYoutubeThumbnailUrlAttribute()
    {
        return 'https://img.youtube.com/vi/' . $this->youtube_video_id . '/hqdefault.jpg';
    }
}
