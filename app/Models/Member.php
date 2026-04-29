<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use App\Traits\BelongsToGym;
use Illuminate\Foundation\Auth\User as Authenticatable;
use App\Models\TrainerSchedule;

class Member extends Authenticatable
{
    use BelongsToGym;

    protected $fillable = [
        'gym_id', 'member_no', 'name', 'email', 'phone', 'password',
        'gender', 'dob', 'address',
        'emergency_contact_name', 'emergency_contact_phone',
        'avatar', 'status', 'goal', 'whatsapp_optin',
        'blood_group', 'occupation', 'joined_at', 'notes',
    ];

    protected $casts = [
        'dob'             => 'date',
        'joined_at'       => 'date',
        'whatsapp_optin'  => 'boolean',
        'password'        => 'hashed',
    ];

    // ── Relationships ──────────────────────────────────────────────

    public function gym(): BelongsTo
    {
        return $this->belongsTo(Gym::class);
    }

    public function memberPlans(): HasMany
    {
        return $this->hasMany(MemberPlan::class);
    }

    public function activePlan(): HasOne
    {
        return $this->hasOne(MemberPlan::class)->where('status', 'active')->latestOfMany();
    }

    public function invoices(): HasMany
    {
        return $this->hasMany(Invoice::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    public function attendances(): HasMany
    {
        return $this->hasMany(Attendance::class);
    }

    public function bodyStats(): HasMany
    {
        return $this->hasMany(BodyStat::class)->orderByDesc('date');
    }

    public function latestBodyStat(): HasOne
    {
        return $this->hasOne(BodyStat::class)->latestOfMany('date');
    }

    public function progressPhotos(): HasMany
    {
        return $this->hasMany(ProgressPhoto::class)->orderByDesc('date');
    }

    public function workoutPlan(): HasOne
    {
        return $this->hasOne(WorkoutPlan::class)->where('is_active', true)->latestOfMany();
    }

    public function workoutProgress(): HasOne
    {
        return $this->hasOne(MemberWorkoutProgress::class);
    }

    public function dietPlan(): HasOne
    {
        return $this->hasOne(DietPlan::class)->where('is_active', true)->latestOfMany();
    }

    public function trainer()
    {
        return $this->belongsToMany(Trainer::class, 'trainer_members', 'member_id', 'trainer_id')
                    ->withPivot('assigned_at')
                    ->withTimestamps();
    }

    public function trainerSchedules()
    {
        return $this->belongsToMany(
            TrainerSchedule::class,
            'trainer_schedule_members',
            'member_id',
            'schedule_id'
        )->withPivot('assigned_at', 'start_date', 'end_date')->withTimestamps();
    }

    public function whatsappLogs(): HasMany
    {
        return $this->hasMany(WhatsappLog::class);
    }

    public function aiMessages(): HasMany
    {
        return $this->hasMany(MemberAiMessage::class);
    }

    // ── Helpers ────────────────────────────────────────────────────

    public function getAvatarUrlAttribute(): string
    {
        if ($this->avatar) {
            return asset('storage/' . $this->avatar);
        }
        return asset('images/default-member.png');
    }

    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    public function isPlanExpiring(int $days = 3): bool
    {
        return $this->activePlan &&
               $this->activePlan->end_date->diffInDays(now()) <= $days &&
               $this->activePlan->end_date->isFuture();
    }
}
