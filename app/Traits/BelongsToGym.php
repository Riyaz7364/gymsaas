<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Builder;

trait BelongsToGym
{
    public static function bootBelongsToGym(): void
    {
        // Auto-scope queries to current gym when authenticated (non-super-admin)
        static::addGlobalScope('gym', function (Builder $builder) {
            if (auth()->check() && ! auth()->user()->isSuperAdmin()) {
                $gymId = auth()->user()->gym_id;
                if ($gymId) {
                    $builder->where(
                        (new static)->getTable() . '.gym_id',
                        $gymId
                    );
                }
            }
        });
    }

    public static function withoutGymScope(): Builder
    {
        return static::withoutGlobalScope('gym');
    }
}
