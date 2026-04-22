<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WorkoutSequenceExercise extends Model
{
    protected $table = 'workout_sequence_exercises';

    protected $fillable = [
        'day_id', 'activity_id', 'name', 'sort_order',
    ];

    public function day(): BelongsTo
    {
        return $this->belongsTo(WorkoutSequenceDay::class, 'day_id');
    }

    public function activity(): BelongsTo
    {
        return $this->belongsTo(WorkoutActivity::class, 'activity_id');
    }
}
