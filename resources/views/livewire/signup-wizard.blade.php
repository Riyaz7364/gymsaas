<div wire:key="signup-step-{{ $step }}">
    @include('livewire.signup.steps.' . $step, [
        'plans' => $plans,
        'selectedPlan' => $selectedPlan,
        'allModules' => $allModules,
        'includedModuleKeys' => $includedModuleKeys,
    ])
</div>
