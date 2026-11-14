<?php

namespace App\Models;

use App\Enums\SessionCategory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SessionsProgram extends Model
{
    use HasFactory;

    protected $fillable = ['programs_id','day', 'category', 'priority', 'year', 'start_time', 'end_time', 'subject_en', 'subject_ar', 'presentation_en', 'presentation_ar','facilitator_id','title_en','title_ar'];

    protected $casts = ['category' => SessionCategory::class, 'priority' => 'integer'];

    protected $attributes = ['category' => 'sessions', 'priority' => 0];

//speaker

    public function facilitator(){
        return $this->belongsTo(Speakers::class, 'facilitator_id');
    }
    public function speakers(){
        return $this->belongsToMany(Speakers::class, 'session_speaker')->withTimestamps();
    }

}
