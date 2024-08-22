<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class News extends Model
{
        use HasFactory;

        protected $fillable = [
            'title_en',
            'title_ar',
            'author_en',
            'author_ar',
            'desc_en',
            'desc_ar',
            'image',
            'event_time'
        ];
}
