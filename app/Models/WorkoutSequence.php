<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Traits\BelongsToGym;

class WorkoutSequence extends Model
{
    use BelongsToGym;

    protected $fillable = [
        'gym_id', 'name', 'description', 'total_days', 'is_default', 'is_active',
    ];

    protected $casts = [
        'is_default' => 'boolean',
        'is_active' => 'boolean',
    ];

    public function gym(): BelongsTo
    {
        return $this->belongsTo(Gym::class);
    }

    public function days(): HasMany
    {
        return $this->hasMany(WorkoutSequenceDay::class, 'sequence_id')->orderBy('day_number');
    }

    /**
     * Get the day for a given step number
     */
    public function getDayByNumber(int $dayNumber): ?WorkoutSequenceDay
    {
        return $this->days()->where('day_number', $dayNumber)->first();
    }

    /**
     * Get the default sequence for a gym
     */
    public static function getDefault(int $gymId): ?self
    {
        return self::where('gym_id', $gymId)
            ->where('is_default', true)
            ->where('is_active', true)
            ->first();
    }
}
