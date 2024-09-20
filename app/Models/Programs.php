<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Programs extends Model
{
    use HasFactory;
    protected $fillable = ['year', 'day1', 'day2', 'day3', 'day4', 'day5',];


    //get days from program
    public function sessionsProgram(): HasMany
    {
        return $this->hasMany(SessionsProgram::class);
    }
}
