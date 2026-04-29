<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GymSubscription extends Model
{
    protected $fillable = [
        'gym_id', 'razorpay_subscription_id', 'plan_id', 'status', 'expires_at', 'subscription_data',
    ];

    protected $casts = [
        'expires_at'        => 'datetime',
        'subscription_data' => 'array',
    ];

    public function gym(): BelongsTo
    {
        return $this->belongsTo(Gym::class);
    }

    public function plan(): BelongsTo
    {
        return $this->belongsTo(SubscriptionPlan::class, 'plan_id');
    }

    public function isActive(): bool
    {
        return in_array($this->status, ['active', 'trial']);
    }
}
