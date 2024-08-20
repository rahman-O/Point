<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SessionsProgram extends Model
{
    use HasFactory;

    protected $fillable = ['programs_id', 'name', 'day', 'year', 'start_time', 'end_time', 'sub_en', 'sub_ar', 'pres_en', 'pres_ar', 'speaker_id','facilitator_id'];


}
