<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class EnsureGymActive
{
    public function handle(Request $request, Closure $next)
    {
        $user = $request->user();

        if (! $user || $user->isSuperAdmin()) {
            return $next($request);
        }

        if (! $user->gym_id || ! $user->gym) {
            abort(403, 'No gym associated with your account. Please contact support.');
        }

        if ($user->gym->status === 'suspended') {
            abort(403, 'Your gym account has been suspended. Please contact support.');
        }

        return $next($request);
    }
}
