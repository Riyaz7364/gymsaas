<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Traits\BelongsToGym;

class WorkoutPlan extends Model
{
    use BelongsToGym;

    protected $fillable = [
        'gym_id', 'member_id', 'trainer_id',
        'name', 'description', 'is_default', 'is_active',
    ];

    protected $casts = ['is_default' => 'boolean', 'is_active' => 'boolean'];

    public function gym(): BelongsTo        { return $this->belongsTo(Gym::class); }
    public function member(): BelongsTo     { return $this->belongsTo(Member::class); }
    public function trainer(): BelongsTo    { return $this->belongsTo(Trainer::class); }

    public function items(): HasMany
    {
        return $this->hasMany(WorkoutPlanItem::class, 'plan_id')->orderBy('day_of_week')->orderBy('sort_order');
    }

    public function todayItems(): HasMany
    {
        $day = strtolower(now()->format('D')); // mon, tue, wed...
        return $this->hasMany(WorkoutPlanItem::class, 'plan_id')
                    ->where('day_of_week', $day)
                    ->orderBy('sort_order');
    }
}
