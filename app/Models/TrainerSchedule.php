<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class TrainerSchedule extends Model
{
    const DAY_LABELS = [
        'mon' => 'Monday',
        'tue' => 'Tuesday',
        'wed' => 'Wednesday',
        'thu' => 'Thursday',
        'fri' => 'Friday',
        'sat' => 'Saturday',
        'sun' => 'Sunday',
    ];

    const DAY_SHORT = [
        'mon' => 'Mon', 'tue' => 'Tue', 'wed' => 'Wed',
        'thu' => 'Thu', 'fri' => 'Fri', 'sat' => 'Sat', 'sun' => 'Sun',
    ];

    protected $fillable = [
        'gym_id', 'trainer_id', 'title', 'day_of_week',
        'start_time', 'end_time', 'max_members', 'price', 'notes', 'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'price'     => 'decimal:2',
    ];

    public function trainer(): BelongsTo
    {
        return $this->belongsTo(Trainer::class);
    }

    public function members(): BelongsToMany
    {
        return $this->belongsToMany(
            Member::class,
            'trainer_schedule_members',
            'schedule_id',
            'member_id'
        )->withPivot('assigned_at', 'start_date', 'end_date')
         ->withTimestamps();
    }

    public function isFull(): bool
    {
        return $this->members()->count() >= $this->max_members;
    }

    public function availableSlots(): int
    {
        return max(0, $this->max_members - $this->members()->count());
    }

    public function getDayLabelAttribute(): string
    {
        return self::DAY_LABELS[$this->day_of_week] ?? ucfirst($this->day_of_week);
    }

    public function getTimeRangeAttribute(): string
    {
        return Carbon::parse($this->start_time)->format('h:i A')
             . ' – '
             . Carbon::parse($this->end_time)->format('h:i A');
    }
}
