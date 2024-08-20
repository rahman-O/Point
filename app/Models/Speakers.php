<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Speakers extends Model
{
    use HasFactory;

    protected $fillable = [
        'name_en',
        'name_ar',
        'job_en',
        'job_ar',
        'country_en',
        'country_ar',
        'year',
        'desc_en',
        'desc_ar',
        'image',
    ];

    public function sessions()
    {
        return $this->belongsToMany(SessionsProgram::class, 'session_speaker')->withTimestamps();
    }
}
