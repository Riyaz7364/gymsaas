<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use App\Traits\BelongsToGym;
use App\Models\TrainerSchedule;

class Trainer extends Authenticatable
{
    use BelongsToGym;

    protected $fillable = [
        'gym_id', 'user_id', 'name', 'email', 'phone', 'username', 'password', 'avatar',
        'specialization', 'bio', 'experience_years', 'salary', 'status', 'joined_at',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'joined_at' => 'date',
        'password'  => 'hashed',
    ];

    public function gym(): BelongsTo  { return $this->belongsTo(Gym::class); }
    public function user(): BelongsTo { return $this->belongsTo(User::class); }

    public function members(): BelongsToMany
    {
        return $this->belongsToMany(Member::class, 'trainer_members', 'trainer_id', 'member_id')
                    ->withPivot('assigned_at')
                    ->withTimestamps();
    }

    public function classes(): HasMany
    {
        return $this->hasMany(GymClass::class);
    }

    public function workoutPlans(): HasMany
    {
        return $this->hasMany(WorkoutPlan::class);
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(TrainerReview::class);
    }

    public function memberHistories(): HasMany
    {
        return $this->hasMany(TrainerMemberHistory::class);
    }

    public function schedules(): HasMany
    {
        return $this->hasMany(TrainerSchedule::class)
                    ->orderByRaw("FIELD(day_of_week,'mon','tue','wed','thu','fri','sat','sun')")
                    ->orderBy('start_time');
    }

    public function activeSchedules(): HasMany
    {
        return $this->hasMany(TrainerSchedule::class)
                    ->where('is_active', true)
                    ->orderByRaw("FIELD(day_of_week,'mon','tue','wed','thu','fri','sat','sun')")
                    ->orderBy('start_time');
    }

    public function getAvatarUrlAttribute(): string
    {
        return $this->avatar ? asset('storage/' . $this->avatar) : asset('images/default-trainer.png');
    }
}
