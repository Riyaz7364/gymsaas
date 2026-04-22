<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Traits\BelongsToGym;

class GymClass extends Model
{
    protected $table = 'classes';

    use BelongsToGym;

    protected $fillable = [
        'gym_id', 'trainer_id', 'name', 'description',
        'schedule_days', 'start_time', 'end_time', 'capacity', 'room', 'status',
    ];

    protected $casts = [
        'schedule_days' => 'array',
    ];

    public function gym(): BelongsTo     { return $this->belongsTo(Gym::class); }
    public function trainer(): BelongsTo { return $this->belongsTo(Trainer::class); }

    public function bookings(): HasMany
    {
        return $this->hasMany(ClassBooking::class, 'class_id');
    }
}
