<?php

namespace App\Models;

use App\Enums\SessionCategory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SessionsProgram extends Model
{
    use HasFactory;

    protected $fillable = ['programs_id','day', 'category', 'priority', 'year', 'start_time', 'end_time', 'subject_en', 'subject_ar', 'presentation_en', 'presentation_ar','facilitator_id','rapporteur_id','trainer_id','chair_id','lecturer_id','participants','title_en','title_ar'];

    protected $casts = ['category' => SessionCategory::class, 'priority' => 'integer', 'participants' => 'array'];

    protected $attributes = ['category' => 'sessions', 'priority' => 0];

    /**
     * Optional single-holder roles an item's speakers can hold; each is a nullable speaker
     * FK column. The master of ceremonies (العريف) can be held by several speakers and
     * lives in the mcs() pivot instead.
     *
     * @var list<string>
     */
    public const ROLE_COLUMNS = ['facilitator_id', 'rapporteur_id', 'trainer_id', 'chair_id', 'lecturer_id'];

    public function facilitator(){
        return $this->belongsTo(Speakers::class, 'facilitator_id');
    }
    public function rapporteur(){
        return $this->belongsTo(Speakers::class, 'rapporteur_id');
    }
    public function trainer(){
        return $this->belongsTo(Speakers::class, 'trainer_id');
    }
    public function chair(){
        return $this->belongsTo(Speakers::class, 'chair_id');
    }
    public function lecturer(){
        return $this->belongsTo(Speakers::class, 'lecturer_id');
    }
    /** Masters of ceremonies (العريف): any number of the item's speakers. */
    public function mcs(){
        return $this->belongsToMany(Speakers::class, 'session_mc')->withTimestamps()->orderBy('session_mc.id');
    }
    public function speakers(){
        return $this->belongsToMany(Speakers::class, 'session_speaker')->withTimestamps();
    }

}
