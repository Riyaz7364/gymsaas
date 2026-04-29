@php
    $settings = app(\App\Services\SignupSettings::class);
    $trialDays = $settings->get('trial_days', 30);
    $trialBadge = $settings->get('trial_badge', 'Recommended');
    $annualDisc = $settings->get('annual_discount_pct', 10);
    $trialOn = (bool) $settings->get('trial_enabled', true);
    $subscribeOn = (bool) $settings->get('subscribe_enabled', true);
@endphp

<div class="su-card" x-data="{ loading: false }">
    <h1 class="su-card-title">How would you like to start?</h1>
    <p class="su-card-sub">Choose the option that works best for you</p>

    @if($errors->any())
        <div class="su-alert danger" style="margin-bottom:16px;">
            @foreach($errors->all() as $error)<div>{{ $error }}</div>@endforeach
        </div>
    @endif

    <form wire:submit.prevent="saveStartType" @submit="loading = true">
        @if($trialOn)
            <label class="su-type">
                <div class="su-badge-g">{{ $trialBadge }}</div>
                <div class="su-type-hd">
                    <div class="su-type-ico green">T</div>
                    <div class="su-type-body">
                        <div class="su-type-title">{{ $settings->get('trial_card_title', 'Start Free Trial') }}</div>
                        <div class="su-type-desc">Try {{ config('app.name') }} free for {{ $trialDays }} days</div>
                        <div class="su-type-green">No credit card required</div>
                    </div>
                    <input type="radio" wire:model.live="start_type" value="trial" class="su-type-radio">
                </div>
            </label>
        @endif

        @if($subscribeOn)
            <label class="su-type">
                {{-- <div class="   ">SAVE {{ $annualDisc }}%</div> --}}
                <div class="su-type-hd">
                    <div class="su-type-ico gray">S</div>
                    <div class="su-type-body">
                        <div class="su-type-title">{{ $settings->get('sub_card_title', 'Subscribe & Save') }}</div>
                        <div class="su-type-desc">Skip trial and get started immediately</div>
                        <div class="su-type-green">Secure payment & checkout</div>

                        @if($start_type === 'subscribe')
                            <div class="su-billing-toggle" style="display:flex;">
                                <label class="su-billing-opt">
                                    <input type="radio" wire:model.live="billing_cycle" value="monthly" style="accent-color:#0abf8e;cursor:pointer;">
                                    Monthly
                                </label>
                                <label class="su-billing-opt">
                                    <input type="radio" wire:model.live="billing_cycle" value="annual" style="accent-color:#0abf8e;cursor:pointer;">
                                    Annual
                                    <span class="su-billing-save">Save {{ $annualDisc }}%</span>
                                </label>
                            </div>
                        @endif
                    </div>
                    <input type="radio" wire:model.live="start_type" value="subscribe" class="su-type-radio">
                </div>
            </label>
        @endif

        @error('start_type')<p class="su-error">{{ $message }}</p>@enderror
        @error('billing_cycle')<p class="su-error">{{ $message }}</p>@enderror

        <div class="su-row-btns">
            <button type="button" wire:click="goTo('gym')" class="su-btn-ghost">Back</button>
            <button type="submit" class="su-btn su-btn-primary" :disabled="loading">
                <span x-show="!loading">Choose a Plan</span>
                <span x-show="loading" style="display:none;">Processing...</span>
            </button>
        </div>
    </form>
</div>
