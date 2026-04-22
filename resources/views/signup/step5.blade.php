<x-layouts.signup :currentStep="5">
    <x-slot:title>Enhance Your Plan — {{ config('app.name') }}</x-slot:title>

    @php
        $isTrial     = session('signup.step3.start_type') === 'trial';
        $billing     = session('signup.step3.billing_cycle', 'monthly');
        $planId      = session('signup.step4.plan_id');
        $selectedPlan = $plan;

        $basePriceMonthly = $selectedPlan
            ? (float) ($billing === 'annual' ? $selectedPlan->annual_price : $selectedPlan->monthly_price)
            : 0;
        $planLabel  = $selectedPlan ? ($selectedPlan->display_name ?? $selectedPlan->name) : 'Your Plan';
    @endphp

    <div class="su-card wide" x-data="enhancePage({{ $basePriceMonthly }})">

        {{-- Title row --}}
        <div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:4px;">
            <h1 class="su-card-title" style="margin:0;">Enhance your plan</h1>
            <a href="{{ route('signup.step5.post') }}" class="su-link" style="font-size:13px; display:flex; align-items:center; gap:3px;"
               onclick="event.preventDefault(); document.getElementById('skip-form').submit();">
                Skip for now
                <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3"/></svg>
            </a>
        </div>
        <form id="skip-form" method="POST" action="{{ route('signup.step5.post') }}" style="display:none;">
            @csrf
        </form>

        <p class="su-card-sub" style="margin-bottom:14px;">
            @if($selectedPlan) Recommended Add-ons for <strong>{{ $planLabel }}</strong> — Get features from higher plans without upgrading @endif
        </p>

        {{-- Info box --}}
        @if($selectedPlan)
            <div class="su-alert info" style="display:flex; align-items:center; gap:8px; margin-bottom:8px; font-size:13px;">
                <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><circle cx="12" cy="12" r="10"/><path stroke-linecap="round" d="M12 8v4m0 4h.01"/></svg>
                Recommended Add-ons for {{ $planLabel }} — Get features from higher plans without upgrading
            </div>
        @endif

        <form method="POST" action="{{ route('signup.step5.post') }}" @submit="loading = true" id="enhance-form">
            @csrf

            {{-- Add-on cards grid --}}
            @php $addons = $allModules->where('price', '>', 0); @endphp
            @if($addons->isEmpty())
                <div style="text-align:center; padding:24px; color:#6b7280; font-size:13px;">
                    No add-ons available at this time.
                </div>
            @else
                <div class="su-addon-grid">
                    @foreach($addons as $addon)
                        <div class="su-addon"
                             :class="{ checked: isChecked('{{ $addon->key }}') }"
                             @click="toggle('{{ $addon->key }}')">
                            <input type="checkbox"
                                   class="su-addon-chk"
                                   name="selected_addons[]"
                                   value="{{ $addon->key }}"
                                   :checked="isChecked('{{ $addon->key }}')"
                                   @click.stop="toggle('{{ $addon->key }}')"
                                   data-price="{{ $addon->price }}"
                                   data-billing="{{ $addon->billing_type }}"
                                   data-label="{{ $addon->label }}">
                            <div class="su-addon-icon">{{ $addon->icon ?? '✨' }}</div>
                            <div class="su-addon-name">{{ $addon->label }}</div>
                            <div>
                                <span class="su-addon-price">₹{{ number_format($addon->price, 0) }}</span>
                                <span class="su-addon-cycle">/{{ $addon->billing_type === 'one_time' ? 'one-time' : 'mo' }}</span>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif

            {{-- Also include plan-included modules (hidden checkboxes) --}}
            @foreach($allModules->where('price', null)->whereIn('key', $includedModuleKeys) as $mod)
                <input type="hidden" name="enabled_modules[]" value="{{ $mod->key }}">
            @endforeach

            {{-- Your Selection summary --}}
            <div class="su-sel">
                <div class="su-sel-title">Your Selection</div>

                <div class="su-sel-row">
                    <span class="su-sel-l">{{ $planLabel }} ({{ ucfirst($billing) }})</span>
                    <span class="su-sel-v">₹<span x-text="basePriceFormatted"></span>/mo</span>
                </div>

                <template x-for="addon in selectedAddons" :key="addon.key">
                    <div class="su-sel-row">
                        <span class="su-sel-l" x-text="addon.label"></span>
                        <span class="su-sel-v">
                            + ₹<span x-text="addon.price.toLocaleString('en-IN')"></span>
                            <span x-text="addon.billing === 'one_time' ? ' one-time' : '/mo'" style="font-size:11px; color:#6b7280;"></span>
                        </span>
                    </div>
                </template>

                <div class="su-sel-total">
                    <span class="su-sel-tl">Monthly Total</span>
                    <span class="su-sel-tv">₹<span x-text="totalFormatted"></span>/mo</span>
                </div>
                <a href="#" class="su-sel-annual">✨ Save 17% with annual billing</a>
                @if($isTrial)
                    <span class="su-sel-note">Add-ons will be activated after your Trial ends</span>
                @endif
            </div>

            <div class="su-row-btns" style="margin-top:20px;">
                <a href="{{ route('signup.step4') }}" class="su-btn-ghost">
                    <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18"/></svg>
                    Back
                </a>
                <button type="submit" class="su-btn su-btn-primary" :disabled="loading">
                    <span x-show="!loading">Continue</span>
                    <span x-show="loading" style="display:none;">Saving…</span>
                    <svg x-show="!loading" width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3"/></svg>
                </button>
            </div>
        </form>
    </div>

    <script>
    function enhancePage(basePrice) {
        return {
            loading: false,
            base: basePrice,
            checkedKeys: [],

            get basePriceFormatted() {
                return this.base.toLocaleString('en-IN');
            },

            get selectedAddons() {
                return this.checkedKeys.map(k => {
                    const el = document.querySelector(`[data-billing][value="${k}"]`);
                    if (!el) return null;
                    return {
                        key: k,
                        label: el.dataset.label,
                        price: parseFloat(el.dataset.price) || 0,
                        billing: el.dataset.billing
                    };
                }).filter(Boolean);
            },

            get totalFormatted() {
                const addonsTotal = this.selectedAddons
                    .filter(a => a.billing === 'monthly')
                    .reduce((s, a) => s + a.price, 0);
                return (this.base + addonsTotal).toLocaleString('en-IN');
            },

            isChecked(key) { return this.checkedKeys.includes(key); },

            toggle(key) {
                const i = this.checkedKeys.indexOf(key);
                if (i === -1) this.checkedKeys.push(key);
                else this.checkedKeys.splice(i, 1);
            }
        };
    }
    </script>
</x-layouts.signup>
