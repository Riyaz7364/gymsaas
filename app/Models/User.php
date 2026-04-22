<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

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
        if ($this->isSuperAdmin()) {
            return true;
        }
        $gym = $this->gym;
        return $gym ? $gym->hasModule($module) : false;
    }
}
