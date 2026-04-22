<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Traits\BelongsToGym;

class MemberWorkoutProgress extends Model
{
    use BelongsToGym;

    protected $table = 'member_workout_progress';

    protected $fillable = [
        'gym_id', 'member_id', 'sequence_id', 'current_step', 'total_steps', 'last_checkin_date',
    ];

    protected $casts = [
        'last_checkin_date' => 'date',
    ];

    // -- Sequences are now stored in database --
    // WorkoutSequence model manages reusable templates with days and exercises.
    // Each member is assigned a default sequence which they progress through on check-in.


    // ── Relationships ──────────────────────────────────────────────────────────

    public function gym(): BelongsTo   { return $this->belongsTo(Gym::class); }
    public function member(): BelongsTo { return $this->belongsTo(Member::class); }
    public function sequence(): BelongsTo { return $this->belongsTo(WorkoutSequence::class, 'sequence_id'); }

    // ── Helpers ───────────────────────────────────────────────────────────────

    /**
     * Return the current day's workout data (from the assigned sequence).
     */
    public function currentWorkout(): ?WorkoutSequenceDay
    {
        if (!$this->sequence) {
            return null;
        }
        return $this->sequence->getDayByNumber($this->current_step);
    }

    /**
     * Advance the step by 1 on check-in.
     * Wraps around after total_steps. Will not advance twice on the same day.
     */
    public function advance(): void
    {
        $today = today()->toDateString();

        if ($this->last_checkin_date && $this->last_checkin_date->toDateString() === $today) {
            return; // already advanced today
        }

        $this->current_step      = ($this->current_step % $this->total_steps) + 1;
        $this->last_checkin_date = today();
        $this->save();
    }

    /**
     * Resolve or bootstrap a progress record for the given member.
     * Assigns the default sequence for the gym.
     */
    public static function forMember(int $memberId, int $gymId): static
    {
        $progress = static::firstOrCreate(
            ['member_id' => $memberId, 'gym_id' => $gymId],
            [
                'current_step' => 1,
                'last_checkin_date' => null,
            ]
        );

        // Assign default sequence if not already assigned
        if (!$progress->sequence_id) {
            $defaultSequence = WorkoutSequence::getDefault($gymId);
            if ($defaultSequence) {
                $progress->sequence_id = $defaultSequence->id;
                $progress->total_steps = $defaultSequence->total_days;
                $progress->save();
            }
        }

        return $progress;
    }
}
