<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\DB;

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

    protected static function booted(): void
    {
        // `year` is the legacy single-year column (kept as the newest year); make sure it is always listed.
        static::saved(function (Speakers $speaker) {
            if (self::isYear($speaker->year)) {
                $speaker->years()->firstOrCreate(['year' => trim($speaker->year)]);
            }
        });
    }

    public function sessions()
    {
        return $this->belongsToMany(SessionsProgram::class, 'session_speaker')->withTimestamps();
    }

    public function years(): HasMany
    {
        return $this->hasMany(SpeakerYear::class, 'speakers_id');
    }

    public function scopeInYear(Builder $query, string|int $year): Builder
    {
        return $query->whereHas('years', fn (Builder $years) => $years->where('year', (string) $year));
    }

    /**
     * @return list<string> newest first
     */
    public function yearList(): array
    {
        $years = $this->years->pluck('year')->all() ?: [$this->year];

        return self::cleanYears($years);
    }

    public function syncYears(array $years): void
    {
        $years = self::cleanYears($years);
        if (! $years) {
            return;
        }

        DB::transaction(function () use ($years) {
            $this->years()->whereNotIn('year', $years)->delete();
            foreach ($years as $year) {
                $this->years()->firstOrCreate(['year' => $year]);
            }
            if ((string) $this->year !== $years[0]) {
                $this->update(['year' => $years[0]]);
            }
        });

        $this->unsetRelation('years');
    }

    public function isUsedInAgenda(): bool
    {
        return $this->sessions()->exists()
            || SessionsProgram::where('facilitator_id', $this->getKey())->exists();
    }

    /**
     * @return list<string>
     */
    private static function cleanYears(array $years): array
    {
        $years = array_values(array_unique(array_filter(
            array_map(fn ($year) => trim((string) $year), $years),
            fn ($year) => self::isYear($year),
        )));
        rsort($years);

        return $years;
    }

    private static function isYear($value): bool
    {
        return (bool) preg_match('/^\d{4}$/', trim((string) $value));
    }
}
