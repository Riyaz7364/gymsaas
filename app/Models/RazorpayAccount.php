<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RazorpayAccount extends Model
{
    protected $fillable = [
        'gym_id', 'razorpay_account_id', 'status', 'kyc_status', 'account_data',
    ];

    protected $casts = [
        'account_data' => 'array',
    ];

    public function gym(): BelongsTo
    {
        return $this->belongsTo(Gym::class);
    }

    public function isActive(): bool
    {
        return $this->status === 'activated' && $this->kyc_status === 'approved';
    }
}
