<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TrainerMemberHistory extends Model
{
    protected $fillable = [
        'gym_id',
        'trainer_id',
        'member_id',
        'action',
        'related_plan_id',
        'notes',
        'occurred_at',
    ];

    protected $casts = [
        'occurred_at' => 'date',
    ];

    public function trainer(): BelongsTo
    {
        return $this->belongsTo(Trainer::class);
    }

    public function member(): BelongsTo
    {
        return $this->belongsTo(Member::class);
    }

    public function relatedPlan(): BelongsTo
    {
        return $this->belongsTo(MemberPlan::class, 'related_plan_id');
    }
}
