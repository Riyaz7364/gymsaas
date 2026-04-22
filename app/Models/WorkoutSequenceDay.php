<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class WorkoutSequenceDay extends Model
{
    protected $table = 'workout_sequence_days';

    protected $fillable = [
        'sequence_id', 'day_number', 'label', 'icon', 'color', 'bg', 'border', 'muscle_groups',
    ];

    protected $casts = [
        'muscle_groups' => 'array',
    ];

    public function sequence(): BelongsTo
    {
        return $this->belongsTo(WorkoutSequence::class, 'sequence_id');
    }

    public function exercises(): HasMany
    {
        return $this->hasMany(WorkoutSequenceExercise::class, 'day_id')->orderBy('sort_order');
    }
}
