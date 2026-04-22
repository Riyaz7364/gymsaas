<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Traits\BelongsToGym;

class Payment extends Model
{
    use BelongsToGym;

    protected $fillable = [
        'gym_id', 'invoice_id', 'member_id',
        'amount', 'method', 'gateway_txn_id', 'gateway_order_id',
        'gateway_response', 'status', 'notes', 'paid_at',
    ];

    protected $casts = [
        'paid_at'          => 'datetime',
        'gateway_response' => 'array',
        'amount'           => 'decimal:2',
    ];

    public function gym(): BelongsTo     { return $this->belongsTo(Gym::class); }
    public function invoice(): BelongsTo { return $this->belongsTo(Invoice::class); }
    public function member(): BelongsTo  { return $this->belongsTo(Member::class); }
}
