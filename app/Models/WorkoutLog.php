<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Traits\BelongsToGym;

class WorkoutLog extends Model
{
    use BelongsToGym;

    protected $fillable = [
        'gym_id', 'member_id', 'plan_item_id', 'activity_id',
        'date', 'sets_done', 'reps_done', 'weight_kg', 'duration_secs', 'notes',
    ];

    protected $casts = ['date' => 'date'];

    public function gym(): BelongsTo      { return $this->belongsTo(Gym::class); }
    public function member(): BelongsTo   { return $this->belongsTo(Member::class); }
    public function activity(): BelongsTo { return $this->belongsTo(WorkoutActivity::class); }
}
