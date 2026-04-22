<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;

/**
 * Signup settings stored in storage/app/signup-settings.json.
 * No DB migration needed — falls back to sensible defaults.
 */
class SignupSettings
{
    private string $path;

    /** Default values for every setting */
    private array $defaults = [
        // Trial
        'trial_enabled'       => true,
        'trial_days'          => 30,
        'trial_badge'         => 'Recommended',

        // Subscribe & Save
        'subscribe_enabled'   => true,
        'annual_discount_pct' => 10,

        // Step 3 badge copy
        'trial_card_title'    => 'Start Free Trial',
        'trial_card_desc'     => 'Try the platform free for {days} days',
        'sub_card_title'      => 'Subscribe & Save',

        // Feature bullets (trial card)
        'trial_features'      => [
            'Full access to all features for {days} days',
            'Add unlimited members during trial',
            'No commitment, cancel anytime',
            'Easy upgrade when ready',
        ],

        // Feature bullets (subscribe card)
        'sub_features'        => [
            'Save {disc}% with annual billing',
            'Choose what is needed for your business',
            'Priority onboarding support',
            'Locked-in pricing guarantee',
        ],

        // Step 4 —free-trial banner copy
        'trial_banner'        => '🎁 Free {days}-day trial unlocked! Choose your plan below.',
    ];

    public function __construct()
    {
        $this->path = storage_path('app/signup-settings.json');
    }

    /** Read all settings, merged on top of defaults. */
    public function all(): array
    {
        return array_replace_recursive($this->defaults, $this->load());
    }

    /** Get a single setting with optional default fallback. */
    public function get(string $key, mixed $fallback = null): mixed
    {
        return $this->all()[$key] ?? $fallback;
    }

    /** Persist a map of settings (merges on top of existing). */
    public function save(array $data): void
    {
        $existing = $this->load();
        $merged   = array_replace_recursive($existing, $data);
        file_put_contents($this->path, json_encode($merged, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
        Cache::forget('signup_settings');
    }

    // ── Private ──────────────────────────────────────────────────────

    private function load(): array
    {
        return Cache::remember('signup_settings', 300, function () {
            if (! file_exists($this->path)) {
                return [];
            }
            $decoded = json_decode(file_get_contents($this->path), true);
            return is_array($decoded) ? $decoded : [];
        });
    }
}
