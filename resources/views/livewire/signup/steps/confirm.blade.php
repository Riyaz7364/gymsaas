@php
    $isTrial = $start_type === 'trial';
    $billing = $billing_cycle ?: 'monthly';
    $gymName = $gym_name ?: 'Your Gym';
    $addons = session('signup.step5.selected_addons', []);
    $planLabel = $selectedPlan ? ($selectedPlan->display_name ?? $selectedPlan->name) : 'Starter';
    $planPrice = $selectedPlan ? (float) ($billing === 'annual' ? $selectedPlan->annual_price : $selectedPlan->monthly_price) : 0;
    $addonTotal = collect($addons)->where('billing_type', 'monthly')->sum('price');
    $monthlyTotal = $planPrice + $addonTotal;
@endphp

<div class="su-card wide" x-data="{ loading: false }">
    <div class="su-vhdr-icon" style="width:56px;height:56px;font-size:28px;">+</div>

    <h1 class="su-card-title" style="text-align:center; margin-bottom:4px;">
        {{ $isTrial ? 'Start Your Free Trial' : 'Create Your Account' }}
    </h1>
    <p class="su-card-sub" style="text-align:center; margin-bottom:22px;">
        {{ $isTrial ? 'No payment required. Cancel anytime.' : 'Get started immediately with ' . ucfirst($billing) . ' billing.' }}
    </p>

    <div class="su-confirm-box">
        <div class="{{ $isTrial ? 'su-confirm-box-hd' : 'su-confirm-paid-hd' }}">
            <span>{{ $isTrial ? 'Free Trial - No Payment Required' : 'Subscribe & Save - ' . ucfirst($billing) . ' Billing' }}</span>
        </div>

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
                <span class="su-confirm-val">{{ number_format($addon['price'], 0) }} {{ ($addon['billing_type'] ?? '') === 'one_time' ? 'one-time' : '/mo' }}</span>
            </div>
        @endforeach

        <div class="su-confirm-row su-confirm-total-row">
            <span class="su-confirm-lbl" style="font-weight:700; color:#111827;">{{ $isTrial ? 'After Trial' : 'Monthly Total' }}</span>
            <span class="su-confirm-val" style="font-size:15px;">{{ number_format($monthlyTotal, 0) }}/mo</span>
        </div>
    </div>

    <form wire:submit.prevent="completeSignup" @submit="loading = true">
        <div class="su-row-btns">
            <button type="button" wire:click="goTo('enhance')" class="su-btn-ghost">Back</button>
            <button type="submit" class="su-btn su-btn-primary" :disabled="loading">
                <span x-show="!loading">{{ $isTrial ? 'Start Free Trial' : 'Create My Account' }}</span>
                <span x-show="loading" style="display:none;">Creating...</span>
            </button>
        </div>
    </form>
</div>
