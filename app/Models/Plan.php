<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Traits\BelongsToGym;

class Plan extends Model
{
    use BelongsToGym;

    protected $fillable = [
        'gym_id', 'name', 'type', 'duration_days', 'price', 'description', 'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'price'     => 'decimal:2',
    ];

    public function gym(): BelongsTo
    {
        return $this->belongsTo(Gym::class);
    }

    public function memberPlans(): HasMany
    {
        return $this->hasMany(MemberPlan::class);
    }
}
