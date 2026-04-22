<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Gym extends Model
{
    protected $fillable = [
        'name', 'slug', 'owner_id', 'subscription_plan', 'status',
        'logo', 'address', 'city', 'state', 'country',
        'phone', 'email', 'website',
        'currency', 'timezone', 'date_format', 'language',
        'whatsapp_dispatch_time',
    ];

    // ── Relationships ──────────────────────────────────────────────

    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    public function members(): HasMany
    {
        return $this->hasMany(Member::class);
    }

    public function plans(): HasMany
    {
        return $this->hasMany(Plan::class);
    }

    public function trainers(): HasMany
    {
        return $this->hasMany(Trainer::class);
    }

    public function settings(): HasMany
    {
        return $this->hasMany(GymSetting::class);
    }

    public function subscriptions(): HasMany
    {
        return $this->hasMany(GymSubscription::class);
    }

    public function activeSubscription(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(GymSubscription::class)
            ->whereIn('status', ['active', 'trial'])
            ->latestOfMany();
    }

    // ── Settings helpers ───────────────────────────────────────────

    public function getSetting(string $key, mixed $default = null): mixed
    {
        $setting = $this->settings()->where('key', $key)->first();
        return $setting ? $setting->value : $default;
    }

    public function setSetting(string $key, mixed $value): void
    {
        $this->settings()->updateOrCreate(['key' => $key], ['value' => $value]);
    }

    // ── Helpers ────────────────────────────────────────────────────

    public function getLogoUrlAttribute(): string
    {
        if ($this->logo) {
            return asset('storage/' . $this->logo);
        }
        return asset('images/default-gym-logo.png');
    }

    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    /**
     * Check if the gym's active subscription plan includes a premium module.
     * Returns false when no subscription or plan has no modules set.
     */
    public function hasModule(string $module): bool
    {
        $subscription = $this->activeSubscription;
        if (!$subscription || !$subscription->plan) {
            return false;
        }
        return $subscription->plan->hasModule($module);
    }
}
