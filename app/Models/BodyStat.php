<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Traits\BelongsToGym;

class BodyStat extends Model
{
    use BelongsToGym;

    protected $fillable = [
        'gym_id', 'member_id', 'date',
        'weight', 'height', 'bmi', 'body_fat_pct', 'muscle_mass',
        'chest', 'waist', 'hips', 'arms', 'thighs', 'notes',
    ];

    protected $casts = ['date' => 'date'];

    public function gym(): BelongsTo    { return $this->belongsTo(Gym::class); }
    public function member(): BelongsTo { return $this->belongsTo(Member::class); }
}
