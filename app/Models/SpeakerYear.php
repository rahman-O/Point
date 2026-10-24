<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SpeakerYear extends Model
{
    protected $fillable = ['speakers_id', 'year'];

    public function speaker(): BelongsTo
    {
        return $this->belongsTo(Speakers::class, 'speakers_id');
    }
}
