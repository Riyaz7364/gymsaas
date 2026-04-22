<x-layouts.signup :currentStep="4">
    <x-slot:title>Choose Plan — {{ config('app.name') }}</x-slot:title>

    @php
        $isTrial   = session('signup.step3.start_type') === 'trial';
        $billing   = session('signup.step3.billing_cycle', 'monthly');
        $trialDays = app(\App\Services\SignupSettings::class)->get('trial_days', 30);
    @endphp

    @php
        $defaultPlanId = old('plan_id', $plans->skip(1)->first()?->id ?? $plans->first()?->id ?? '');
    @endphp

    <div class="su-card" x-data="{ loading: false }" style="max-width:580px;">

        {{-- Banner --}}
        @if($isTrial)
            <div class="su-alert success" style="display:flex; align-items:center; gap:10px; margin-bottom:20px;">
                <span style="font-size:20px;">🎁</span>
                <div>
                    <div style="font-weight:700; color:#15803d;">FREE Trial Unlocked!</div>
                    <div style="font-size:12px;">No payment needed. Just choose a plan that suits your requirements!</div>
                </div>
            </div>
        @else
            <div class="su-alert info" style="display:flex; align-items:center; gap:10px; margin-bottom:20px;">
                <span style="font-size:20px;">💳</span>
                <div>
                    <div style="font-weight:700;">Subscribe &amp; Save</div>
                    <div style="font-size:12px;">Billing cycle: <strong>{{ ucfirst($billing) }}</strong></div>
                </div>
            </div>
        @endif

        <h1 class="su-card-title" style="text-align:center;">Choose your plan</h1>
        <p class="su-card-sub" style="text-align:center;">
            All plans include a 30-day free trial. <a href="#" class="su-link">Upgrade</a> or cancel anytime.
        </p>

        @if($errors->any())
            <div class="su-alert danger">{{ $errors->first('plan_id') }}</div>
        @endif

        @if($plans->isEmpty())
            <div style="text-align:center; padding:32px; color:#6b7280;">
                <div style="font-size:36px; margin-bottom:8px;">📋</div>
                <p style="margin:0;">No subscription plans available yet. Please contact support.</p>
            </div>
        @else
            <form method="POST" action="{{ route('signup.step4.post') }}" @submit="loading = true">
                @csrf

                @foreach($plans as $index => $plan)
                    @php
                        $price = $billing === 'annual' ? $plan->annual_price : $plan->monthly_price;
                    @endphp
                    {{-- Plain onclick: no Alpine dependency for radio selection --}}
                    <div class="su-plan" onclick="document.getElementById('plan_{{ $plan->id }}').click()">

                        @if($index === 1)
                            <div class="su-plan-pop">⭐ Popular</div>
                        @endif

                        <div class="su-plan-hd">
                            <div class="su-plan-info">
                                <div class="su-plan-name-price">
                                    <span class="su-plan-name">{{ $plan->display_name ?? $plan->name }}</span>
                                    <span class="su-plan-price">
                                        ₹{{ number_format($price, 0) }}<span class="su-plan-unit">/mo</span>
                                    </span>
                                </div>
                                <div class="su-plan-free">FREE for {{ $isTrial ? $trialDays : '30' }} days</div>
                                @if($plan->description ?? false)
                                    <div class="su-plan-desc">{{ $plan->description }}</div>
                                @else
                                    <div class="su-plan-desc">
                                        @if($index === 0) Perfect for small gyms
                                        @elseif($index === 1) For growing fitness centers
                                        @else For established gyms @endif
                                    </div>
                                @endif
                                <div class="su-plan-tags">
                                    <span class="su-plan-tag">∞ members</span>
                                    <span class="su-plan-tag">
                                        @if($plan->max_trainers >= 9999) ∞ staff
                                        @else {{ $plan->max_trainers }} staff @endif
                                    </span>
                                    @if($plan->max_classes && $plan->max_classes < 9999)
                                        <span class="su-plan-tag">{{ $plan->max_classes }} classes</span>
                                    @endif
                                </div>
                            </div>
                            <div class="su-plan-radio-wrap">
                                <input type="radio" id="plan_{{ $plan->id }}" name="plan_id" value="{{ $plan->id }}"
                                       onclick="event.stopPropagation()"
                                       @checked($defaultPlanId == $plan->id)
                                       style="width:18px; height:18px; accent-color:#4f46e5; cursor:pointer;">
                            </div>
                        </div>
                    </div>
                @endforeach

                <div class="su-plan-tip">
                    <span>💡</span>
                    <span>You can change your plan anytime during or after the trial</span>
                </div>

                <div class="su-row-btns" style="margin-top:20px;">
                    <a href="{{ route('signup.step3') }}" class="su-btn-ghost">
                        <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18"/></svg>
                        Back
                    </a>
                    <button type="submit" class="su-btn su-btn-primary" :disabled="loading">
                        <span x-show="!loading">Create My Gym →</span>
                        <span x-show="loading" style="display:none;">Processing…</span>
                    </button>
                </div>
            </form>
        @endif
    </div>

</x-layouts.signup>
