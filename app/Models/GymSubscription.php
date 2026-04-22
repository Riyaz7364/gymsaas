<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GymSubscription extends Model
{
    protected $fillable = [
        'gym_id', 'plan_id', 'status', 'billing_cycle',
        'amount', 'started_at', 'expires_at', 'cancelled_at', 'notes',
    ];

    protected $casts = [
        'started_at'    => 'date',
        'expires_at'    => 'date',
        'cancelled_at'  => 'date',
        'amount'        => 'decimal:2',
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
