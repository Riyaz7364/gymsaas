<?php

namespace App\Http\Controllers;

use App\Support\GymModuleRegistry;

class ModuleController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $gym = $user->gym;
        $enabledKeys = collect(GymModuleRegistry::userKeys($user));

        $modules = GymModuleRegistry::groupedDefinitions()
            ->map(function ($groupModules) use ($enabledKeys) {
                return collect($groupModules)->map(function (array $module) use ($enabledKeys) {
                    $module['enabled'] = $enabledKeys->contains($module['key']);

                    return $module;
                })->values();
            });

        return view('modules.index', compact('modules', 'gym'));
    }
}
