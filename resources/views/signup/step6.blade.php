<x-layouts.signup :currentStep="6">
    <x-slot:title>Confirm & Start — {{ config('app.name') }}</x-slot:title>

    @php
        $isTrial    = session('signup.step3.start_type') === 'trial';
        $billing    = session('signup.step3.billing_cycle', 'monthly');
        $gymName    = session('signup.step2.gym_name', 'Your Gym');
        $addons     = session('signup.step5.selected_addons', []);

        $planLabel  = $plan ? ($plan->display_name ?? $plan->name) : 'Starter';
        $planPrice  = $plan ? (float)($billing === 'annual' ? $plan->annual_price : $plan->monthly_price) : 0;
        $addonTotal = collect($addons)->where('billing_type', 'monthly')->sum('price');
        $monthlyTotal = $planPrice + $addonTotal;
    @endphp

    <div class="su-card wide">

        {{-- Icon --}}
        <div class="su-vhdr-icon" style="width:56px;height:56px;font-size:28px;">✨</div>

        <h1 class="su-card-title" style="text-align:center; margin-bottom:4px;">
            {{ $isTrial ? 'Start Your Free Trial' : 'Create Your Account' }}
        </h1>
        <p class="su-card-sub" style="text-align:center; margin-bottom:22px;">
            {{ $isTrial ? 'No payment required • Cancel anytime' : 'Get started immediately with ' . ucfirst($billing) . ' billing' }}
        </p>

        {{-- Summary box --}}
        <div class="su-confirm-box">
            @if($isTrial)
                <div class="su-confirm-box-hd">
                    <span>✨ Free Trial — No Payment Required</span>
                </div>
            @else
                <div class="su-confirm-paid-hd">
                    <span>💳 Subscribe &amp; Save — {{ ucfirst($billing) }} Billing</span>
                </div>
            @endif

            <div class="su-confirm-row">
                <span class="su-confirm-lbl">Plan</span>
                <span class="su-confirm-val">{{ $planLabel }}</span>
            </div>
            <div class="su-confirm-row">
                <span class="su-confirm-lbl">{{ $isTrial ? 'Trial Period' : 'Billing Cycle' }}</span>
                <span class="su-confirm-val">{{ $isTrial ? '30 days free' : ucfirst($billing) }}</span>
            </div>
            <div class="su-confirm-row">
                <span class="su-confirm-lbl">Gym</span>
                <span class="su-confirm-val">{{ $gymName }}</span>
            </div>

            @foreach($addons as $addon)
                <div class="su-confirm-row">
                    <span class="su-confirm-lbl">{{ $addon['label'] ?? $addon['key'] }}</span>
                    <span class="su-confirm-val">
                        ₹{{ number_format($addon['price'], 0) }}
                        {{ ($addon['billing_type'] ?? '') === 'one_time' ? ' one-time' : '/mo' }}
                    </span>
                </div>
            @endforeach

            <div class="su-confirm-row su-confirm-total-row">
                <span class="su-confirm-lbl" style="font-weight:700; color:#111827;">{{ $isTrial ? 'After Trial' : 'Monthly Total' }}</span>
                <span class="su-confirm-val" style="font-size:15px;">₹{{ number_format($monthlyTotal, 0) }}/mo</span>
            </div>
        </div>

        {{-- Notes --}}
        <ul style="list-style:none; padding:0; margin:0 0 20px; display:flex; flex-direction:column; gap:5px;">
            @if($isTrial)
                <li style="font-size:13px; color:#374151;">✅ No credit card required to start</li>
                <li style="font-size:13px; color:#374151;">✅ Full access to all features for 30 days</li>
                <li style="font-size:13px; color:#374151;">✅ Upgrade or cancel anytime</li>
            @else
                <li style="font-size:13px; color:#374151;">✅ Immediate full access</li>
                <li style="font-size:13px; color:#374151;">✅ Secure payment</li>
                <li style="font-size:13px; color:#374151;">✅ Cancel anytime</li>
            @endif
        </ul>

        <form method="POST" action="{{ route('signup.complete') }}" x-data="{ loading: false }" @submit="loading = true">
            @csrf

            <div class="su-row-btns">
                <a href="{{ route('signup.step5') }}" class="su-btn-ghost">
                    <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18"/></svg>
                    Back
                </a>
                <button type="submit" class="su-btn su-btn-primary" :disabled="loading">
                    <span x-show="!loading">{{ $isTrial ? '⭐ Start Free Trial' : '🚀 Create My Account' }}</span>
                    <span x-show="loading" style="display:none;">Creating…</span>
                    <svg x-show="!loading" width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3"/></svg>
                </button>
            </div>
        </form>
    </div>
</x-layouts.signup>
