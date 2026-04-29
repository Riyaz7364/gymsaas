<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckGymModule
{
    /**
     * Enforce premium module access at the HTTP level.
     *
     * Usage in routes:  ->middleware('module:whatsapp_updates')
     *
     * - Super admins bypass all checks (for previewing).
     * - If the gym's active plan does not include the module, the request
     *   is aborted with a 403 "Module not available" page.
     */
    public function handle(Request $request, Closure $next, string $module): Response
    {
        
        $user = $request->user();

        if (!$user) {
            abort(403);
        }

        // Super admins always have access
        if ($user->isSuperAdmin()) {
            return $next($request);
        }

        if (!$user->gymHasModule($module)) {
            $moduleLabel = \App\Support\GymModuleRegistry::label($module);

            if ($request->expectsJson()) {
                return response()->json([
                    'error'   => 'module_not_available',
                    'message' => "Your plan does not include {$moduleLabel}. Please upgrade to access this feature.",
                ], 403);
            }

            abort(403, "Your plan does not include {$moduleLabel}. Please upgrade your subscription to access this feature.");
        }

        return $next($request);
    }
}
