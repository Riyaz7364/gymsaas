<?php

namespace App\Support;

use App\Models\Gym;
use App\Models\Module;
use App\Models\SubscriptionPlan;
use App\Models\User;
use Illuminate\Support\Collection;

class GymModuleRegistry
{
    public static function definitions(): Collection
    {
        static $definitions;

        if ($definitions instanceof Collection) {
            return $definitions;
        }

        $dbModules = Module::query()->get()->keyBy('key');

        $definitions = collect(config('gym-modules.definitions', []))
            ->map(function (array $definition, string $key) use ($dbModules) {
                $dbModule = $dbModules->get($key);

                return array_merge([
                    'key' => $key,
                    'label' => $dbModule?->label ?? ucwords(str_replace('_', ' ', $key)),
                    'description' => $dbModule?->description,
                    'group' => $dbModule?->group_name ?? 'General',
                    'icon' => $dbModule?->icon,
                    'entry_route' => null,
                ], $definition);
            })
            ->sortBy([
                ['group', 'asc'],
                ['label', 'asc'],
            ])
            ->values();

        return $definitions;
    }

    public static function groupedDefinitions(): Collection
    {
        return self::definitions()->groupBy('group');
    }

    public static function find(string $key): ?array
    {
        return self::definitions()->firstWhere('key', $key);
    }

    public static function label(string $key): string
    {
        return self::find($key)['label'] ?? ucwords(str_replace('_', ' ', $key));
    }

    public static function planDefaultKeys(?string $planName): array
    {
        if (!$planName) {
            return [];
        }

        return config('gym-modules.plan_defaults.' . strtolower($planName), []);
    }

    public static function planKeys(?SubscriptionPlan $plan, ?string $fallbackPlanName = null): array
    {
        if ($plan) {
            $plan->loadMissing('modules');

            if ($plan->modules->isNotEmpty()) {
                return $plan->modules->pluck('key')->values()->all();
            }

            $fallbackPlanName ??= $plan->name;
        }

        return self::planDefaultKeys($fallbackPlanName);
    }

    public static function gymAddonKeys(?Gym $gym): array
    {
        if (!$gym) {
            return [];
        }

        $raw = $gym->getSetting('enabled_modules', '[]');

        if (is_array($raw)) {
            return array_values(array_filter($raw));
        }

        $decoded = json_decode((string) $raw, true);

        return is_array($decoded)
            ? array_values(array_filter($decoded))
            : [];
    }

    public static function gymKeys(?Gym $gym): array
    {
        if (!$gym) {
            return [];
        }

        $subscription = $gym->activeSubscription;
        $planKeys = self::planKeys($subscription?->plan, $gym->subscription_plan);
        $addonKeys = self::gymAddonKeys($gym);

        return array_values(array_unique(array_merge($planKeys, $addonKeys)));
    }

    public static function userKeys(?User $user): array
    {
        if (!$user) {
            return [];
        }

        if ($user->isSuperAdmin()) {
            return self::definitions()->pluck('key')->all();
        }

        return self::gymKeys($user->gym);
    }

    public static function userHasModule(?User $user, string $key): bool
    {
        return in_array($key, self::userKeys($user), true);
    }

    public static function getModulesForUser(?User $user): Collection
    {
        $keys = self::gymKeys($user->gym);

        return self::definitions()->whereIn('key', $keys);
    }
}
