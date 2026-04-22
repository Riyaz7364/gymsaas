<?php

namespace App\Livewire;

use App\Models\Gym;
use App\Models\GymSetting;
use App\Models\GymSubscription;
use App\Models\Module;
use App\Models\SubscriptionPlan;
use App\Models\User;
use App\Services\SignupSettings;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Livewire\Component;
use Spatie\Permission\Models\Role;

class SignupWizard extends Component
{
    public string $step = 'account';

    public string $name = '';
    public string $email = '';
    public string $phone = '';
    public string $password = '';
    public string $password_confirmation = '';
    public bool $terms = false;

    public bool $emailVerified = false;
    public bool $phoneVerified = false;
    public string $emailError = '';
    public string $phoneError = '';

    public string $gym_name = '';
    public string $address = '';
    public string $city = '';
    public string $state = '';
    public string $country = 'India';
    public string $currency = 'INR';
    public string $timezone = 'Asia/Kolkata';

    public string $start_type = '';
    public string $billing_cycle = 'monthly';

    public string $plan_id = '';

    public array $selected_addons = [];
    public array $enabled_modules = [];

    protected array $stepOrder = [
        'account',
        'verify',
        'gym',
        'start-type',
        'plan',
        'enhance',
        'confirm',
    ];

    public function mount(): void
    {
        $this->hydrateFromSession();
        $this->step = $this->normalizeStep($this->step);
    }

    public function saveAccount(): void
    {
        $validated = $this->validate([
            'name' => ['required', 'string', 'max:100'],
            'email' => ['required', 'email', 'max:150', 'unique:users,email'],
            'phone' => ['required', 'string', 'max:20'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'password_confirmation' => ['required'],
            'terms' => ['accepted'],
        ], [
            'terms.accepted' => 'You must agree to continue.',
        ]);

        session()->put('signup.step1', [
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'],
            'password' => $validated['password'],
        ]);
        session()->put('signup.otp_verified_email', false);
        session()->put('signup.otp_verified_phone', false);

        $this->emailVerified = false;
        $this->phoneVerified = false;
        $this->emailError = '';
        $this->phoneError = '';

        $this->goTo('verify');
    }

    public function verifyEmail(string $otp): void
    {
        $this->emailError = '';

        if (! session()->has('signup.step1')) {
            $this->goTo('account');
            return;
        }

        if (trim($otp) !== '123456') {
            $this->emailError = 'Invalid code. Please try again.';
            return;
        }

        session(['signup.otp_verified_email' => true]);
        $this->emailVerified = true;
    }

    public function verifyPhone(string $otp): void
    {
        $this->phoneError = '';

        if (! $this->emailVerified) {
            $this->phoneError = 'Please verify your email first.';
            return;
        }

        if (trim($otp) !== '123456') {
            $this->phoneError = 'Invalid code. Please try again.';
            return;
        }

        session(['signup.otp_verified_phone' => true]);
        $this->phoneVerified = true;
        $this->goTo('gym');
    }

    public function saveGym(): void
    {
        if (! $this->stepOneComplete()) {
            $this->goTo($this->firstIncompleteStep());
            return;
        }

        $validated = $this->validate([
            'gym_name' => ['required', 'string', 'max:100'],
            'address' => ['nullable', 'string', 'max:255'],
            'city' => ['required', 'string', 'max:60'],
            'state' => ['required', 'string', 'max:60'],
            'country' => ['required', 'string', 'max:60'],
            'currency' => ['required', 'string', 'max:5'],
            'timezone' => ['required', 'string', 'max:60'],
        ]);

        session()->put('signup.step2', $validated);
        $this->goTo('start-type');
    }

    public function saveStartType(): void
    {
        if (! $this->stepTwoComplete()) {
            $this->goTo($this->firstIncompleteStep());
            return;
        }

        $validated = $this->validate([
            'start_type' => ['required', 'in:trial,subscribe'],
            'billing_cycle' => ['required_if:start_type,subscribe', 'nullable', 'in:monthly,annual'],
        ]);

        if ($validated['start_type'] === 'trial') {
            $validated['billing_cycle'] = 'monthly';
        }

        session()->put('signup.step3', $validated);
        $this->goTo('plan');
    }

    public function savePlan(): void
    {
        if (! $this->stepThreeComplete()) {
            $this->goTo($this->firstIncompleteStep());
            return;
        }

        $validated = $this->validate([
            'plan_id' => ['required', 'exists:subscription_plans,id'],
        ]);

        session()->put('signup.step4', $validated);

        $this->refreshStepFiveState();
        $this->goTo('enhance');
    }

    public function saveEnhancements(): void
    {
        if (! $this->stepFourComplete()) {
            $this->goTo($this->firstIncompleteStep());
            return;
        }

        $addonModules = Module::whereIn('key', $this->selected_addons)->get();
        $addonData = $addonModules->map(fn ($module) => [
            'key' => $module->key,
            'label' => $module->label,
            'price' => (float) $module->price,
            'billing_type' => $module->billing_type,
        ])->values()->toArray();

        session()->put('signup.step5', [
            'selected_addons' => $addonData,
            'enabled_modules' => $this->enabled_modules,
        ]);

        $this->goTo('confirm');
    }

    public function skipEnhancements(): void
    {
        $this->selected_addons = [];
        $this->saveEnhancements();
    }

    public function completeSignup()
    {
        if (! $this->stepFiveComplete()) {
            $this->goTo($this->firstIncompleteStep());
            return null;
        }

        $step1 = session('signup.step1');
        $step2 = session('signup.step2');
        $step3 = session('signup.step3');
        $step4 = session('signup.step4');
        $step5 = session('signup.step5', []);

        DB::transaction(function () use ($step1, $step2, $step3, $step4, $step5) {
            $user = User::create([
                'name' => $step1['name'],
                'email' => $step1['email'],
                'phone' => $step1['phone'],
                'password' => Hash::make($step1['password']),
                'status' => 'active',
            ]);

            $role = Role::firstOrCreate(['name' => 'gym_owner', 'guard_name' => 'web']);
            $user->assignRole($role);

            $slug = Str::slug($step2['gym_name']) . '-' . Str::random(4);
            $gym = Gym::create([
                'name' => $step2['gym_name'],
                'slug' => strtolower($slug),
                'owner_id' => $user->id,
                'status' => 'active',
                'address' => $step2['address'] ?? null,
                'city' => $step2['city'],
                'state' => $step2['state'],
                'country' => $step2['country'],
                'currency' => $step2['currency'],
                'timezone' => $step2['timezone'],
                'phone' => $step1['phone'],
                'email' => $step1['email'],
            ]);

            $user->gym_id = $gym->id;
            $user->save();

            $plan = SubscriptionPlan::findOrFail($step4['plan_id']);
            $isTrial = $step3['start_type'] === 'trial';
            $cycle = $step3['billing_cycle'] ?? 'monthly';
            $amount = $isTrial ? 0 : ($cycle === 'annual' ? $plan->annual_price : $plan->monthly_price);
            $status = $isTrial ? 'trial' : 'active';
            $trialDays = app(SignupSettings::class)->get('trial_days', 30);
            $expiresAt = $isTrial
                ? now()->addDays((int) $trialDays)
                : ($cycle === 'annual' ? now()->addYear() : now()->addMonth());

            GymSubscription::create([
                'gym_id' => $gym->id,
                'plan_id' => $plan->id,
                'status' => $status,
                'billing_cycle' => $isTrial ? 'monthly' : $cycle,
                'amount' => $amount,
                'started_at' => now(),
                'expires_at' => $expiresAt,
            ]);

            $addonKeys = collect($step5['selected_addons'] ?? [])->pluck('key')->toArray();
            $enabledModuleKeys = array_values(array_unique(array_merge(
                $step5['enabled_modules'] ?? [],
                $addonKeys,
            )));

            if ($enabledModuleKeys !== []) {
                GymSetting::updateOrCreate(
                    ['gym_id' => $gym->id, 'key' => 'enabled_modules'],
                    ['value' => json_encode($enabledModuleKeys)]
                );
            }

            Auth::login($user);
        });

        session()->forget('signup');
        session()->flash('success', 'Welcome to ' . config('app.name') . '! Your gym account is ready.');

        return $this->redirectRoute('dashboard');
    }

    public function goBack(): void
    {
        $index = array_search($this->step, $this->stepOrder, true);

        if ($index === false || $index === 0) {
            $this->goTo('account');
            return;
        }

        $this->goTo($this->stepOrder[$index - 1]);
    }

    public function goTo(string $step): void
    {
        $this->resetErrorBag();
        $this->resetValidation();
        $this->step = $this->normalizeStep($step);
    }

    public function render()
    {
        $plans = SubscriptionPlan::where('is_active', true)
            ->orderBy('sort_order')
            ->get();

        $selectedPlan = $this->selectedPlan();
        $allModules = Module::where('is_active', true)
            ->orderBy('group_name')
            ->orderBy('sort_order')
            ->get();
        $includedModuleKeys = $selectedPlan?->modules?->pluck('key')->values()->all() ?? [];

        return view('livewire.signup-wizard', [
            'plans' => $plans,
            'selectedPlan' => $selectedPlan,
            'allModules' => $allModules,
            'includedModuleKeys' => $includedModuleKeys,
        ])->extends('layouts.signup', [
            'currentStep' => $this->progressStep(),
            'title' => $this->pageTitle(),
        ]);
    }

    private function hydrateFromSession(): void
    {
        $step1 = session('signup.step1', []);
        $this->name = $step1['name'] ?? $this->name;
        $this->email = $step1['email'] ?? $this->email;
        $this->phone = $step1['phone'] ?? $this->phone;
        $this->password = $step1['password'] ?? $this->password;
        $this->password_confirmation = $step1['password'] ?? $this->password_confirmation;
        $this->terms = session()->has('signup.step1');

        $this->emailVerified = (bool) session('signup.otp_verified_email', false);
        $this->phoneVerified = (bool) session('signup.otp_verified_phone', false);

        $step2 = session('signup.step2', []);
        $this->gym_name = $step2['gym_name'] ?? $this->gym_name;
        $this->address = $step2['address'] ?? $this->address;
        $this->city = $step2['city'] ?? $this->city;
        $this->state = $step2['state'] ?? $this->state;
        $this->country = $step2['country'] ?? $this->country;
        $this->currency = $step2['currency'] ?? $this->currency;
        $this->timezone = $step2['timezone'] ?? $this->timezone;

        $step3 = session('signup.step3', []);
        $this->start_type = $step3['start_type'] ?? $this->start_type;
        $this->billing_cycle = $step3['billing_cycle'] ?? $this->billing_cycle;

        $step4 = session('signup.step4', []);
        $this->plan_id = isset($step4['plan_id']) ? (string) $step4['plan_id'] : $this->plan_id;

        $step5 = session('signup.step5', []);
        $this->enabled_modules = $step5['enabled_modules'] ?? $this->enabled_modules;
        $this->selected_addons = collect($step5['selected_addons'] ?? [])->pluck('key')->values()->all();

        if ($this->stepFourComplete()) {
            $this->refreshStepFiveState(false);
        }
    }

    private function refreshStepFiveState(bool $preserveSelections = true): void
    {
        $plan = $this->selectedPlan();
        $includedKeys = $plan?->modules?->pluck('key')->values()->all() ?? [];
        $selectedAddons = $preserveSelections ? $this->selected_addons : [];

        if ($preserveSelections && session()->has('signup.step5')) {
            $selectedAddons = array_values(array_unique(array_merge(
                collect(session('signup.step5.selected_addons', []))->pluck('key')->all(),
                $this->selected_addons,
            )));
        }

        $this->enabled_modules = $includedKeys;
        $this->selected_addons = $selectedAddons;
    }

    private function normalizeStep(?string $requestedStep): string
    {
        $requestedStep = in_array($requestedStep, $this->stepOrder, true)
            ? $requestedStep
            : $this->firstIncompleteStep();

        if ($this->canAccessStep($requestedStep)) {
            return $requestedStep;
        }

        return $this->firstIncompleteStep();
    }

    private function canAccessStep(string $step): bool
    {
        return match ($step) {
            'account' => true,
            'verify' => session()->has('signup.step1'),
            'gym' => $this->stepOneComplete(),
            'start-type' => $this->stepTwoComplete(),
            'plan' => $this->stepThreeComplete(),
            'enhance' => $this->stepFourComplete(),
            'confirm' => $this->stepFiveComplete(),
            default => false,
        };
    }

    private function firstIncompleteStep(): string
    {
        if (! session()->has('signup.step1')) {
            return 'account';
        }

        if (! $this->stepOneComplete()) {
            return 'verify';
        }

        if (! session()->has('signup.step2')) {
            return 'gym';
        }

        if (! session()->has('signup.step3')) {
            return 'start-type';
        }

        if (! session()->has('signup.step4')) {
            return 'plan';
        }

        if (! session()->has('signup.step5')) {
            return 'enhance';
        }

        return 'confirm';
    }

    private function progressStep(): int
    {
        return match ($this->step) {
            'account', 'verify' => 1,
            'gym' => 2,
            'start-type' => 3,
            'plan' => 4,
            'enhance' => 5,
            'confirm' => 6,
            default => 1,
        };
    }

    private function pageTitle(): string
    {
        return match ($this->step) {
            'account' => 'Create Account',
            'verify' => 'Verify Identity',
            'gym' => 'Your Gym',
            'start-type' => 'Start Type',
            'plan' => 'Choose Plan',
            'enhance' => 'Enhance Your Plan',
            'confirm' => 'Confirm & Start',
            default => 'Sign Up',
        } . ' - ' . config('app.name');
    }

    private function selectedPlan(): ?SubscriptionPlan
    {
        if ($this->plan_id === '') {
            return null;
        }

        return SubscriptionPlan::with('modules')->find($this->plan_id);
    }

    private function stepOneComplete(): bool
    {
        return session()->has('signup.step1')
            && session('signup.otp_verified_email') === true
            && session('signup.otp_verified_phone') === true;
    }

    private function stepTwoComplete(): bool
    {
        return $this->stepOneComplete() && session()->has('signup.step2');
    }

    private function stepThreeComplete(): bool
    {
        return $this->stepTwoComplete() && session()->has('signup.step3');
    }

    private function stepFourComplete(): bool
    {
        return $this->stepThreeComplete() && session()->has('signup.step4');
    }

    private function stepFiveComplete(): bool
    {
        return $this->stepFourComplete() && session()->has('signup.step5');
    }
}
