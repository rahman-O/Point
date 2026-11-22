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
        'priority',
    ];

    protected $casts = ['priority' => 'integer'];

    protected $attributes = ['priority' => 0];

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

    /** Agenda items this speaker is a master of ceremonies (العريف) of. */
    public function mcOfSessions()
    {
        return $this->belongsToMany(SessionsProgram::class, 'session_mc')->withTimestamps();
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
     * Order of the public speaker lists: speakers given a position (1, 2, 3…) come first in
     * that order, then everyone else newest first.
     */
    public function scopeDisplayOrder(Builder $query): Builder
    {
        return $query
            ->orderByRaw('CASE WHEN priority > 0 THEN 0 ELSE 1 END')
            ->orderBy('priority')
            ->orderBy('created_at', 'desc')
            ->orderBy('id', 'desc');
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
        if ($this->sessions()->exists() || $this->mcOfSessions()->exists()) {
            return true;
        }

        return SessionsProgram::query()
            ->where(function (Builder $q) {
                foreach (SessionsProgram::ROLE_COLUMNS as $column) {
                    $q->orWhere($column, $this->getKey());
                }
            })
            ->exists();
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
