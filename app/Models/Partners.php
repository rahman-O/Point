<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Partners extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'image', 'sort_order'];

    protected $casts = ['sort_order' => 'integer'];

    protected static function booted(): void
    {
        // New partners join the end of the footer; admins drag them into place from the list page.
        static::creating(function (Partners $partner): void {
            $partner->sort_order ??= (int) static::query()->max('sort_order') + 1;
        });
    }

    /**
     * Partners in the order admins arranged them for the site footer.
     */
    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderBy('sort_order')->orderBy('id');
    }
}
