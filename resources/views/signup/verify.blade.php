<x-layouts.signup :currentStep="1">
    <x-slot:title>Verify Identity — {{ config('app.name') }}</x-slot:title>

    <livewire:verify-otp />
</x-layouts.signup>
