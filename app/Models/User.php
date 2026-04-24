<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;
use App\Support\GymModuleRegistry;
use Illuminate\Support\Collection;


class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable, HasRoles;

    protected $fillable = [
        'name',
        'email',
        'password',
        'gym_id',
        'phone',
        'avatar',
        'status',
        'last_login_at',
        'last_login_ip',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'last_login_at'     => 'datetime',
            'password'          => 'hashed',
        ];
    }

    // ── Relationships ──────────────────────────────────────────────

    public function gym()
    {
        return $this->belongsTo(Gym::class);
    }

    public function ownedGyms(): HasMany
    {
        return $this->hasMany(Gym::class, 'owner_id');
    }

    public function loginHistories()
    {
        return $this->hasMany(LoginHistory::class);
    }

    // ── Helpers ────────────────────────────────────────────────────

    public function isSuperAdmin(): bool
    {
        return $this->hasRole('super_admin');
    }

    public function isGymOwner(): bool
    {
        return $this->hasRole('gym_owner');
    }

    public function isManager(): bool
    {
        return $this->hasRole('manager');
    }

    public function isTrainer(): bool
    {
        return $this->hasRole('trainer');
    }

    public function getAvatarUrlAttribute(): string
    {
        if ($this->avatar) {
            return asset('storage/' . $this->avatar);
        }
        return asset('images/default-avatar.png');
    }

    /**
     * Check if the current user's gym has a premium module enabled.
     * Super admins always have access to all modules for previewing.
     */
    public function gymHasModule(string $module): bool
    {
        return GymModuleRegistry::userHasModule($this, $module);
    }

    public function gymModules(): Collection
    {
        return GymModuleRegistry::getModulesForUser($this);
    }

    public function membersTabs(): Array {
        $tabs = [
            'plan'       => ['label' => 'Membership', 'icon' => '🎫', 'enabled' => true],
            'attendance' => ['label' => 'Attendance',  'icon' => '📅', 'enabled' => true],
            'diet'       => ['label' => 'Diet Plan',   'icon' => '🥗', 'enabled' => $this->gymHasModule('diet_management')],
            'trainer'    => ['label' => 'Trainer',     'icon' => '🏋️', 'enabled' => $this->gymHasModule('trainer_management')],
            'workout'    => ['label' => 'Workout Plan','icon' => '💪', 'enabled' => $this->gymHasModule('workout_management')],
            'messages'   => ['label' => 'AI Messages', 'icon' => '🤖', 'enabled' => true],
            'health'     => ['label' => 'Body Stats',  'icon' => '📊', 'enabled' => $this->gymHasModule('body_progress')],
            'notes'      => ['label' => 'Notes',       'icon' => '📝', 'enabled' => true],
        ];
        return array_filter($tabs, function($tab) {
            return $tab['enabled'];
        });
    }
}
