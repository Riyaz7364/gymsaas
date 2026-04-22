<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Gym;
use App\Models\GymSetting;
use App\Models\GymSubscription;
use App\Models\Module;
use App\Models\SubscriptionPlan;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use App\Services\SignupSettings;
use Spatie\Permission\Models\Role;

class SignupController extends Controller
{
    // ── Step 1: Account ────────────────────────────────────────────

    public function showStep1()
    {
        return view('signup.step1');
    }

    public function postStep1(Request $request)
    {
        $validated = $request->validate([
            'name'                  => ['required', 'string', 'max:100'],
            'email'                 => ['required', 'email', 'max:150', 'unique:users,email'],
            'phone'                 => ['required', 'string', 'max:20'],
            'password'              => ['required', 'string', 'min:8', 'confirmed'],
            'password_confirmation' => ['required'],
            'terms'                 => ['accepted'],
        ], [
            'terms.accepted' => 'You must agree to continue.',
        ]);

        // Store step-1 data in session
        $request->session()->put('signup.step1', [
            'name'     => $validated['name'],
            'email'    => $validated['email'],
            'phone'    => $validated['phone'],
            'password' => $validated['password'],
        ]);

        // In a real system, send OTP to email/phone.
        // For now, the OTP is always 123456.
        $request->session()->put('signup.otp_verified_email', false);
        $request->session()->put('signup.otp_verified_phone', false);

        return redirect()->route('signup.verify');
    }

    // ── OTP Verification ───────────────────────────────────────────

    public function showVerify()
    {
        if (! session()->has('signup.step1')) {
            return redirect()->route('signup.step1');
        }
        return view('signup.verify');
    }

    public function postVerifyEmail(Request $request)
    {
        $request->validate([
            'otp' => ['required', 'digits:6'],
        ]);

        if ($request->input('otp') !== '123456') {
            return back()->withErrors(['otp' => 'Invalid code. Please try again.'])->with('verify_type', 'email');
        }

        $request->session()->put('signup.otp_verified_email', true);

        return redirect()->route('signup.verify')->with('email_verified', true);
    }

    public function postVerifyPhone(Request $request)
    {
        $request->validate([
            'otp' => ['required', 'digits:6'],
        ]);

        if ($request->input('otp') !== '123456') {
            return back()->withErrors(['otp' => 'Invalid code. Please try again.'])->with('verify_type', 'phone');
        }

        $request->session()->put('signup.otp_verified_phone', true);

        // Both verified → proceed to step 2
        return redirect()->route('signup.step2');
    }

    // ── Step 2: Your Gym ───────────────────────────────────────────

    public function showStep2()
    {
        if (! $this->stepOneComplete()) {
            return redirect()->route('signup.step1');
        }
        return view('signup.step2');
    }

    public function postStep2(Request $request)
    {
        if (! $this->stepOneComplete()) {
            return redirect()->route('signup.step1');
        }

        $validated = $request->validate([
            'gym_name' => ['required', 'string', 'max:100'],
            'address'  => ['nullable', 'string', 'max:255'],
            'city'     => ['required', 'string', 'max:60'],
            'state'    => ['required', 'string', 'max:60'],
            'country'  => ['required', 'string', 'max:60'],
            'currency' => ['required', 'string', 'max:5'],
            'timezone' => ['required', 'string', 'max:60'],
        ]);

        $request->session()->put('signup.step2', $validated);

        return redirect()->route('signup.step3');
    }

    // ── Step 3: Start Type ─────────────────────────────────────────

    public function showStep3()
    {
        if (! $this->stepTwoComplete()) {
            return redirect()->route('signup.step2');
        }
        return view('signup.step3');
    }

    public function postStep3(Request $request)
    {
        if (! $this->stepTwoComplete()) {
            return redirect()->route('signup.step2');
        }

        $validated = $request->validate([
            'start_type'    => ['required', 'in:trial,subscribe'],
            'billing_cycle' => ['required_if:start_type,subscribe', 'nullable', 'in:monthly,annual'],
        ]);

        $request->session()->put('signup.step3', $validated);

        return redirect()->route('signup.step4');
    }

    // ── Step 4: Choose Plan ────────────────────────────────────────

    public function showStep4()
    {
        if (! $this->stepThreeComplete()) {
            return redirect()->route('signup.step3');
        }

        $startType = session('signup.step3.start_type');
        $plans = SubscriptionPlan::where('is_active', true)
            ->orderBy('sort_order')
            ->get();

        return view('signup.step4', compact('plans', 'startType'));
    }

    public function postStep4(Request $request)
    {
        if (! $this->stepThreeComplete()) {
            return redirect()->route('signup.step3');
        }

        $validated = $request->validate([
            'plan_id' => ['required', 'exists:subscription_plans,id'],
        ]);

        $request->session()->put('signup.step4', $validated);

        return redirect()->route('signup.step5');
    }

    // ── Step 5: Enhance ────────────────────────────────────────────

    public function showStep5()
    {
        if (! $this->stepFourComplete()) {
            return redirect()->route('signup.step4');
        }

        $planId = session('signup.step4.plan_id');
        $plan = SubscriptionPlan::with('modules')->find($planId);

        $allModules = Module::where('is_active', true)
            ->orderBy('group_name')
            ->orderBy('sort_order')
            ->get();

        $includedModuleKeys = $plan ? $plan->modules->pluck('key')->toArray() : [];

        return view('signup.step5', compact('allModules', 'includedModuleKeys', 'plan'));
    }

    public function postStep5(Request $request)
    {
        if (! $this->stepFourComplete()) {
            return redirect()->route('signup.step4');
        }

        // Resolve selected add-on module details for summary display
        $selectedKeys   = $request->input('selected_addons', []);
        $enabledKeys    = $request->input('enabled_modules', []);
        $addonModules   = Module::whereIn('key', $selectedKeys)->get();
        $addonData      = $addonModules->map(fn ($m) => [
            'key'          => $m->key,
            'label'        => $m->label,
            'price'        => (float) $m->price,
            'billing_type' => $m->billing_type,
        ])->values()->toArray();

        $request->session()->put('signup.step5', [
            'selected_addons'  => $addonData,
            'enabled_modules'  => $enabledKeys,
        ]);

        return redirect()->route('signup.step6');
    }

    // ── Step 6: Confirm ────────────────────────────────────────────

    public function showStep6()
    {
        if (! $this->stepFourComplete()) {
            return redirect()->route('signup.step4');
        }

        $planId = session('signup.step4.plan_id');
        $plan   = SubscriptionPlan::find($planId);

        return view('signup.step6', compact('plan'));
    }

    // ── Complete: Create Account ───────────────────────────────────

    public function complete(Request $request)
    {
        if (! $this->stepFourComplete()) {
            return redirect()->route('signup.step1');
        }

        $step1 = session('signup.step1');
        $step2 = session('signup.step2');
        $step3 = session('signup.step3');
        $step4 = session('signup.step4');

        DB::transaction(function () use ($step1, $step2, $step3, $step4, $request) {
            // 1. Create User
            $user = User::create([
                'name'     => $step1['name'],
                'email'    => $step1['email'],
                'phone'    => $step1['phone'],
                'password' => Hash::make($step1['password']),
                'status'   => 'active',
            ]);

            // 2. Assign role
            $role = Role::firstOrCreate(['name' => 'gym_owner', 'guard_name' => 'web']);
            $user->assignRole($role);

            // 3. Create Gym
            $slug = Str::slug($step2['gym_name']) . '-' . Str::random(4);
            $gym = Gym::create([
                'name'     => $step2['gym_name'],
                'slug'     => strtolower($slug),
                'owner_id' => $user->id,
                'status'   => 'active',
                'address'  => $step2['address'] ?? null,
                'city'     => $step2['city'],
                'state'    => $step2['state'],
                'country'  => $step2['country'],
                'currency' => $step2['currency'],
                'timezone' => $step2['timezone'],
                'phone'    => $step1['phone'],
                'email'    => $step1['email'],
            ]);

            // 4. Link user to gym
            $user->gym_id = $gym->id;
            $user->save();

            // 5. Create subscription
            $plan     = SubscriptionPlan::find($step4['plan_id']);
            $isTrial  = $step3['start_type'] === 'trial';
            $cycle    = $step3['billing_cycle'] ?? 'monthly';
            $amount   = $isTrial ? 0 : ($cycle === 'annual' ? $plan->annual_price : $plan->monthly_price);
            $status   = $isTrial ? 'trial' : 'active';
            $startsAt = now();
            $trialDays = app(SignupSettings::class)->get('trial_days', 30);
            $expiresAt = $isTrial ? now()->addDays((int) $trialDays) : ($cycle === 'annual' ? now()->addYear() : now()->addMonth());

            GymSubscription::create([
                'gym_id'        => $gym->id,
                'plan_id'       => $plan->id,
                'status'        => $status,
                'billing_cycle' => $isTrial ? 'monthly' : $cycle,
                'amount'        => $amount,
                'started_at'    => $startsAt,
                'expires_at'    => $expiresAt,
            ]);

            // 6. Enable selected add-on modules in gym settings
            $step5 = session('signup.step5', []);
            $addonKeys = collect($step5['selected_addons'] ?? [])->pluck('key')->toArray();
            $enabledModuleKeys = array_merge($step5['enabled_modules'] ?? [], $addonKeys);
            if (! empty($enabledModuleKeys)) {
                GymSetting::updateOrCreate(
                    ['gym_id' => $gym->id, 'key' => 'enabled_modules'],
                    ['value'  => json_encode($enabledModuleKeys)]
                );
            }

            // 7. Log in the new user
            Auth::login($user);

            // 8. Clear signup session
            $request->session()->forget('signup');
        });

        return redirect()->route('dashboard')->with('success', 'Welcome to ' . config('app.name') . '! Your gym account is ready.');
    }

    // ── Private helpers ────────────────────────────────────────────

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
