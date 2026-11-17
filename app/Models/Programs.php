<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Programs extends Model
{
    use HasFactory;

    protected $fillable = ['year', 'day1', 'day2', 'day3', 'day4', 'day5', 'day1_date', 'day2_date', 'day3_date', 'day4_date', 'day5_date'];

    protected $casts = [
        'day1_date' => 'date:Y-m-d',
        'day2_date' => 'date:Y-m-d',
        'day3_date' => 'date:Y-m-d',
        'day4_date' => 'date:Y-m-d',
        'day5_date' => 'date:Y-m-d',
    ];

    protected $appends = ['day_dates'];

    /**
     * Calendar date of each enabled day (YYYY-MM-DD or null), keyed day1…day5, for the
     * public agenda.
     *
     * @return array<string, string|null>
     */
    public function getDayDatesAttribute(): array
    {
        $dates = [];
        foreach (range(1, 5) as $n) {
            if ($this->{"day{$n}"}) {
                $dates["day{$n}"] = $this->{"day{$n}_date"}?->format('Y-m-d');
            }
        }

        return $dates;
    }

    //get days from program
    public function sessionsProgram(): HasMany
    {
        // Agenda items in chronological order; the admin table sorts on its own.
        return $this->hasMany(SessionsProgram::class)
            ->orderBy('start_time')
            ->orderBy('end_time')
            ->orderBy('id');
    }
}
