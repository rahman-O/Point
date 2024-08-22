<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SessionsProgram extends Model
{
    use HasFactory;

    protected $fillable = ['programs_id', 'name', 'day', 'year', 'start_time', 'end_time', 'sub_en', 'sub_ar', 'pres_en', 'pres_ar','facilitator_id'];

//speaker

    public function facilitator(){
        return $this->belongsTo(Speakers::class, 'facilitator_id');
    }
    public function speakers(){
        return $this->belongsToMany(Speakers::class, 'session_speaker')->withTimestamps();
    }

}
