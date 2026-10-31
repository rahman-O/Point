<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Schema;

/**
 * Site-wide key/value settings managed from the admin panel (values are stored as JSON).
 */
class SiteSetting extends Model
{
    protected $fillable = ['key', 'value'];

    protected $casts = [
        'value' => 'json',
    ];

    public static function get(string $key, mixed $default = null): mixed
    {
        if (! static::tableExists()) {
            return $default;
        }

        return static::query()->where('key', $key)->value('value') ?? $default;
    }

    public static function set(string $key, mixed $value): void
    {
        static::query()->updateOrCreate(['key' => $key], ['value' => $value]);
    }

    // Production deploys only run migrations on request, so reads fall back to their defaults
    // (instead of failing) until the table has been created.
    public static function tableExists(): bool
    {
        return once(fn () => Schema::hasTable((new static)->getTable()));
    }
}
