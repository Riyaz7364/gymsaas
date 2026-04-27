<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TrainerReview extends Model
{
    protected $fillable = [
        'gym_id',
        'trainer_id',
        'member_id',
        'rating',
        'comment',
    ];

    public function trainer(): BelongsTo
    {
        return $this->belongsTo(Trainer::class);
    }

    public function member(): BelongsTo
    {
        return $this->belongsTo(Member::class);
    }
}
