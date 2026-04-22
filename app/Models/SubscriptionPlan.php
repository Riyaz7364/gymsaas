<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SubscriptionPlan extends Model
{
    protected $fillable = [
        'name', 'display_name', 'monthly_price', 'annual_price',
        'max_members', 'max_trainers', 'max_classes',
        'features', 'is_active', 'sort_order',
    ];

    protected $casts = [
        'features'      => 'array',
        'is_active'     => 'boolean',
        'monthly_price' => 'decimal:2',
        'annual_price'  => 'decimal:2',
    ];

    public function modules(): BelongsToMany
    {
        return $this->belongsToMany(Module::class, 'subscription_plan_modules');
    }

    public function hasModule(string $module): bool
    {
        $this->loadMissing('modules');
        return $this->modules->contains('key', $module);
    }

    public function gymSubscriptions(): HasMany
    {
        return $this->hasMany(GymSubscription::class, 'plan_id');
    }

    public function activeSubscriptions(): HasMany
    {
        return $this->hasMany(GymSubscription::class, 'plan_id')->where('status', 'active');
    }
}
