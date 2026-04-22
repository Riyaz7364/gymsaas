@php
    $isTrial = $start_type === 'trial';
    $billing = $billing_cycle ?: 'monthly';
    $trialDays = app(\App\Services\SignupSettings::class)->get('trial_days', 30);
@endphp

<div class="su-card" x-data="{ loading: false }" style="max-width:580px;">
    @if($isTrial)
        <div class="su-alert success" style="display:flex; align-items:center; gap:10px; margin-bottom:20px;">
            <div>
                <div style="font-weight:700; color:#15803d;">Free Trial Unlocked!</div>
                <div style="font-size:12px;">No payment needed. Just choose a plan that suits your requirements.</div>
            </div>
        </div>
    @else
        <div class="su-alert info" style="display:flex; align-items:center; gap:10px; margin-bottom:20px;">
            <div>
                <div style="font-weight:700;">Subscribe & Save</div>
                <div style="font-size:12px;">Billing cycle: <strong>{{ ucfirst($billing) }}</strong></div>
            </div>
        </div>
    @endif

    <h1 class="su-card-title" style="text-align:center;">Choose your plan</h1>
    <p class="su-card-sub" style="text-align:center;">Pick the package that matches your gym size and goals.</p>

    @error('plan_id')<div class="su-alert danger">{{ $message }}</div>@enderror

    <form wire:submit.prevent="savePlan" @submit="loading = true">
        @foreach($plans as $index => $plan)
            @php
                $price = $billing === 'annual' ? $plan->annual_price : $plan->monthly_price;
            @endphp
            <label class="su-plan">
                @if($index === 1)
                    <div class="su-plan-pop">Popular</div>
                @endif

                <div class="su-plan-hd">
                    <div class="su-plan-info">
                        <div class="su-plan-name-price">
                            <span class="su-plan-name">{{ $plan->display_name ?? $plan->name }}</span>
                            <span class="su-plan-price">{{ number_format($price, 0) }}<span class="su-plan-unit">/mo</span></span>
                        </div>
                        <div class="su-plan-free">Free for {{ $isTrial ? $trialDays : '30' }} days</div>
                        <div class="su-plan-desc">{{ $plan->description ?: 'Flexible plan for gyms that want room to grow.' }}</div>
                    </div>
                    <div class="su-plan-radio-wrap">
                        <input type="radio" wire:model.live="plan_id" value="{{ $plan->id }}" style="width:18px; height:18px; accent-color:#4f46e5; cursor:pointer;">
                    </div>
                </div>
            </label>
        @endforeach

        <div class="su-row-btns" style="margin-top:20px;">
            <button type="button" wire:click="goTo('start-type')" class="su-btn-ghost">Back</button>
            <button type="submit" class="su-btn su-btn-primary" :disabled="loading">
                <span x-show="!loading">Continue</span>
                <span x-show="loading" style="display:none;">Processing...</span>
            </button>
        </div>
    </form>
</div>
