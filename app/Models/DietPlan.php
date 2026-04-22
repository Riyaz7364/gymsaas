<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Traits\BelongsToGym;

class DietPlan extends Model
{
    use BelongsToGym;

    protected $fillable = [
        'gym_id', 'member_id', 'created_by',
        'name', 'description', 'goal',
        'is_default', 'ai_generated', 'ai_prompt_used', 'is_active',
    ];

    protected $casts = [
        'is_default'   => 'boolean',
        'ai_generated' => 'boolean',
        'is_active'    => 'boolean',
    ];

    public function gym(): BelongsTo       { return $this->belongsTo(Gym::class); }
    public function member(): BelongsTo    { return $this->belongsTo(Member::class); }
    public function createdBy(): BelongsTo { return $this->belongsTo(User::class, 'created_by'); }

    public function meals(): HasMany
    {
        return $this->hasMany(DietMeal::class, 'plan_id')->orderBy('sort_order');
    }

    public function todayMeals(): HasMany
    {
        $day = strtolower(now()->format('D'));
        return $this->hasMany(DietMeal::class, 'plan_id')
                    ->whereIn('day_of_week', [$day, 'all'])
                    ->orderBy('sort_order');
    }
}
