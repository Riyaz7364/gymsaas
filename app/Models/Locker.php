<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Traits\BelongsToGym;

class Locker extends Model
{
    use BelongsToGym;

    protected $fillable = [
        'gym_id', 'locker_no', 'member_id', 'status', 'notes', 'assigned_at', 'expires_at',
    ];

    protected $casts = [
        'assigned_at' => 'date',
        'expires_at'  => 'date',
    ];

    public function gym(): BelongsTo    { return $this->belongsTo(Gym::class); }
    public function member(): BelongsTo { return $this->belongsTo(Member::class); }
}
