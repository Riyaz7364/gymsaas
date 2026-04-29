<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\Gym;

class CheckGymAccess
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
       
        $gymSlug = $request->route('gym');

        // If gym parameter exists in route
        if ($gymSlug) {
            $gym = $gymSlug instanceof Gym ? $gymSlug : Gym::where('slug', $gymSlug)->first();

            if (!$gym) {
                abort(404, 'Gym not found');
            }

            // Check if user belongs to this gym or is super admin
            if (auth()->user()->gym_id !== $gym->id && !auth()->user()->isSuperAdmin()) {
                abort(403, 'Unauthorized access to this gym');
            }


            
            // Store gym in request for easy access
            $request->route()->setParameter('gym', $gym);
        }

        return $next($request);
    }
}

