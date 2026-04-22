<x-layouts.signup :currentStep="3">
    <x-slot:title>Start Type — {{ config('app.name') }}</x-slot:title>

    @php
        $settings    = app(\App\Services\SignupSettings::class);
        $trialDays   = $settings->get('trial_days', 30);
        $trialBadge  = $settings->get('trial_badge', 'Recommended');
        $annualDisc  = $settings->get('annual_discount_pct', 10);
        $trialOn     = (bool) $settings->get('trial_enabled', true);
        $subscribeOn = (bool) $settings->get('subscribe_enabled', true);

        // Pre-select when only one card is active, or restore from old()
        $oldSel = old('start_type', '');
        if ($oldSel === '' && $trialOn  && !$subscribeOn) $oldSel = 'trial';
        if ($oldSel === '' && !$trialOn && $subscribeOn)  $oldSel = 'subscribe';
    @endphp

    {{--
        Alpine manages loading state only.
        Card selection via $refs.radio.click() → real native browser radio click.
        CSS :has(input:checked) handles selected border — no Alpine needed.
        CSS :has([value=subscribe]:checked) shows/hides billing toggle.
    --}}
    <div class="su-card" x-data="{ loading: false }">

        <h1 class="su-card-title">How would you like to start?</h1>
        <p class="su-card-sub">Choose the option that works best for you</p>

        @if($errors->any())
            <div class="su-alert danger" style="margin-bottom:16px;">
                @foreach($errors->all() as $e)<div>{{ $e }}</div>@endforeach
            </div>
        @endif

        <form method="POST" action="{{ route('signup.step3.post') }}" @submit="loading = true">
            @csrf

            @if($trialOn)
            {{-- Plain onclick: no Alpine dependency for radio selection --}}
            <div class="su-type" onclick="document.getElementById('st_trial').click()">
                <div class="su-badge-g">
                    <svg width="11" height="11" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                    {{ $trialBadge }}
                </div>
                <div class="su-type-hd">
                    <div class="su-type-ico green">🎁</div>
                    <div class="su-type-body">
                        <div class="su-type-title">{{ $settings->get('trial_card_title', 'Start Free Trial') }}</div>
                        <div class="su-type-desc">Try {{ config('app.name') }} free for {{ $trialDays }} days</div>
                        <div class="su-type-green">No credit card required</div>
                        <div class="su-type-feats">
                            <div class="su-feat"><svg class="su-feat-check" fill="none" viewBox="0 0 24 24" stroke="#16a34a" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>Full access to all features for {{ $trialDays }} days</div>
                            <div class="su-feat"><svg class="su-feat-check" fill="none" viewBox="0 0 24 24" stroke="#16a34a" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>Add unlimited members during trial</div>
                            <div class="su-feat"><svg class="su-feat-check" fill="none" viewBox="0 0 24 24" stroke="#16a34a" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>No commitment, cancel anytime</div>
                            <div class="su-feat"><svg class="su-feat-check" fill="none" viewBox="0 0 24 24" stroke="#16a34a" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>Easy upgrade when ready</div>
                        </div>
                    </div>
                    {{-- id used by parent onclick; onclick.stopPropagation prevents double-fire --}}
                    <input type="radio" id="st_trial" name="start_type" value="trial"
                           onclick="event.stopPropagation()" @checked($oldSel === 'trial')
                           class="su-type-radio">
                </div>
            </div>
            @endif

            @if($subscribeOn)
            <div class="su-type" onclick="document.getElementById('st_subscribe').click()">
                <div class="su-badge-p">SAVE {{ $annualDisc }}%</div>
                <div class="su-type-hd">
                    <div class="su-type-ico gray">💳</div>
                    <div class="su-type-body">
                        <div class="su-type-title">{{ $settings->get('sub_card_title', 'Subscribe & Save') }}</div>
                        <div class="su-type-desc">Skip trial and get started immediately</div>
                        <div class="su-type-green">Secure payment &amp; Checkout</div>
                        <div class="su-type-feats">
                            <div class="su-feat"><svg class="su-feat-check" fill="none" viewBox="0 0 24 24" stroke="#16a34a" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>Save {{ $annualDisc }}% with annual billing</div>
                            <div class="su-feat"><svg class="su-feat-check" fill="none" viewBox="0 0 24 24" stroke="#16a34a" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>Choose what is needed for your business</div>
                            <div class="su-feat"><svg class="su-feat-check" fill="none" viewBox="0 0 24 24" stroke="#16a34a" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>Priority onboarding support</div>
                            <div class="su-feat"><svg class="su-feat-check" fill="none" viewBox="0 0 24 24" stroke="#16a34a" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>Locked-in pricing guarantee</div>
                        </div>
                        {{-- Billing toggle: onclick.stopPropagation keeps clicks inside here --}}
                        <div class="su-billing-toggle" onclick="event.stopPropagation()">
                            <label class="su-billing-opt">
                                <input type="radio" name="billing_cycle" value="monthly"
                                       @checked(old('billing_cycle', 'monthly') === 'monthly')
                                       style="accent-color:#0abf8e;cursor:pointer;">
                                Monthly
                            </label>
                            <label class="su-billing-opt">
                                <input type="radio" name="billing_cycle" value="annual"
                                       @checked(old('billing_cycle') === 'annual')
                                       style="accent-color:#0abf8e;cursor:pointer;">
                                Annual
                                <span class="su-billing-save">Save {{ $annualDisc }}%</span>
                            </label>
                        </div>
                    </div>
                    <input type="radio" id="st_subscribe" name="start_type" value="subscribe"
                           onclick="event.stopPropagation()" @checked($oldSel === 'subscribe')
                           class="su-type-radio">
                </div>
            </div>
            @endif

            @if(!$subscribeOn)
                <input type="hidden" name="billing_cycle" value="monthly">
            @endif

            <div class="su-row-btns">
                <a href="{{ route('signup.step2') }}" class="su-btn-ghost">
                    <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18"/></svg>
                    Back
                </a>
                <button type="submit" class="su-btn su-btn-primary" :disabled="loading">
                    <span x-show="!loading">Choose a Plan</span>
                    <span x-show="loading" style="display:none;">Processing&hellip;</span>
                    <svg x-show="!loading" width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3"/></svg>
                </button>
            </div>
        </form>
    </div>
</x-layouts.signup>