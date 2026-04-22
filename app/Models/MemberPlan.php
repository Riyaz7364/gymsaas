<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Traits\BelongsToGym;

class MemberPlan extends Model
{
    use BelongsToGym;

    protected $fillable = [
        'gym_id', 'member_id', 'plan_id',
        'start_date', 'end_date',
        'amount', 'discount', 'total_paid',
        'status', 'auto_renew', 'frozen_at', 'freeze_days', 'notes',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date'   => 'date',
        'frozen_at'  => 'datetime',
        'auto_renew' => 'boolean',
    ];

    public function gym(): BelongsTo    { return $this->belongsTo(Gym::class); }
    public function member(): BelongsTo { return $this->belongsTo(Member::class); }
    public function plan(): BelongsTo   { return $this->belongsTo(Plan::class); }

    public function getDaysRemainingAttribute(): int
    {
        if ($this->status !== 'active') return 0;
        return max(0, (int) now()->diffInDays($this->end_date, false));
    }
}
