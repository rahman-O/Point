<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SearchEntry extends Model
{
    protected $guarded = ['id'];

    protected function casts(): array
    {
        return [
            'published_on' => 'date',
            'extra' => 'array',
        ];
    }
}
