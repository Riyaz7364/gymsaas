<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Traits\BelongsToGym;

class Invoice extends Model
{
    use BelongsToGym;

    protected $fillable = [
        'gym_id', 'member_id', 'member_plan_id', 'invoice_no',
        'subtotal', 'tax', 'discount', 'total',
        'amount_paid', 'balance_due', 'status', 'due_date', 'notes',
    ];

    protected $casts = [
        'due_date'    => 'date',
        'subtotal'    => 'decimal:2',
        'tax'         => 'decimal:2',
        'discount'    => 'decimal:2',
        'total'       => 'decimal:2',
        'amount_paid' => 'decimal:2',
        'balance_due' => 'decimal:2',
    ];

    public function gym(): BelongsTo        { return $this->belongsTo(Gym::class); }
    public function member(): BelongsTo     { return $this->belongsTo(Member::class); }
    public function memberPlan(): BelongsTo { return $this->belongsTo(MemberPlan::class); }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }
}
