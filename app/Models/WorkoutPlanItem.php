<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WorkoutPlanItem extends Model
{
    protected $fillable = [
        'plan_id', 'activity_id', 'day_of_week',
        'sets', 'reps', 'duration_secs', 'rest_secs', 'sort_order', 'notes',
    ];

    public function plan(): BelongsTo     { return $this->belongsTo(WorkoutPlan::class, 'plan_id'); }
    public function activity(): BelongsTo { return $this->belongsTo(WorkoutActivity::class); }
}
