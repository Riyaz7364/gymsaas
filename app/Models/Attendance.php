<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Traits\BelongsToGym;

class Attendance extends Model
{
    use BelongsToGym;

    protected $fillable = ['gym_id', 'member_id', 'check_in', 'check_out', 'method', 'notes'];

    protected $casts = [
        'check_in'  => 'datetime',
        'check_out' => 'datetime',
    ];

    public function gym(): BelongsTo    { return $this->belongsTo(Gym::class); }
    public function member(): BelongsTo { return $this->belongsTo(Member::class); }

    public function getDurationMinutesAttribute(): ?int
    {
        if ($this->check_out) {
            return (int) $this->check_in->diffInMinutes($this->check_out);
        }
        return null;
    }
}
