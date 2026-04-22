@php
    $billing = $billing_cycle ?: 'monthly';
    $basePriceMonthly = $selectedPlan ? (float) ($billing === 'annual' ? $selectedPlan->annual_price : $selectedPlan->monthly_price) : 0;
    $planLabel = $selectedPlan ? ($selectedPlan->display_name ?? $selectedPlan->name) : 'Your Plan';
    $addons = $allModules->where('price', '>', 0);
@endphp

<div class="su-card wide" x-data="enhanceStep({ basePrice: {{ $basePriceMonthly }}, selected: $wire.entangle('selected_addons') })">
    <div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:4px;">
        <h1 class="su-card-title" style="margin:0;">Enhance your plan</h1>
        <button type="button" wire:click="skipEnhancements" class="su-link" style="font-size:13px; background:none; border:none; padding:0; cursor:pointer;">
            Skip for now
        </button>
    </div>

    <p class="su-card-sub" style="margin-bottom:14px;">
        Recommended add-ons for <strong>{{ $planLabel }}</strong>.
    </p>

    <form wire:submit.prevent="saveEnhancements">
        @if($addons->isEmpty())
            <div style="text-align:center; padding:24px; color:#6b7280; font-size:13px;">
                No add-ons available at this time.
            </div>
        @else
            <div class="su-addon-grid">
                @foreach($addons as $addon)
                    <label class="su-addon" :class="{ checked: checkedKeys.includes('{{ $addon->key }}') }">
                        <input type="checkbox" class="su-addon-chk" x-model="checkedKeys" value="{{ $addon->key }}">
                        <div class="su-addon-icon">{{ $addon->icon ?? '+' }}</div>
                        <div class="su-addon-name">{{ $addon->label }}</div>
                        <div>
                            <span class="su-addon-price">{{ number_format($addon->price, 0) }}</span>
                            <span class="su-addon-cycle">/{{ $addon->billing_type === 'one_time' ? 'one-time' : 'mo' }}</span>
                        </div>
                        <template x-if="false"></template>
                        <span class="hidden" data-addon-key="{{ $addon->key }}" data-addon-label="{{ $addon->label }}" data-addon-price="{{ $addon->price }}" data-addon-billing="{{ $addon->billing_type }}"></span>
                    </label>
                @endforeach
            </div>
        @endif

        <div class="su-sel">
            <div class="su-sel-title">Your Selection</div>
            <div class="su-sel-row">
                <span class="su-sel-l">{{ $planLabel }} ({{ ucfirst($billing) }})</span>
                <span class="su-sel-v"><span x-text="baseFormatted"></span>/mo</span>
            </div>

            <template x-for="addon in selectedAddons" :key="addon.key">
                <div class="su-sel-row">
                    <span class="su-sel-l" x-text="addon.label"></span>
                    <span class="su-sel-v">+ <span x-text="addon.priceFormatted"></span> <span x-text="addon.billingLabel" style="font-size:11px; color:#6b7280;"></span></span>
                </div>
            </template>

            <div class="su-sel-total">
                <span class="su-sel-tl">Monthly Total</span>
                <span class="su-sel-tv"><span x-text="totalFormatted"></span>/mo</span>
            </div>
        </div>

        <div class="su-row-btns" style="margin-top:20px;">
            <button type="button" wire:click="goTo('plan')" class="su-btn-ghost">Back</button>
            <button type="submit" class="su-btn su-btn-primary">Continue</button>
        </div>
    </form>
</div>

<script>
function enhanceStep(config) {
    return {
        checkedKeys: config.selected,
        basePrice: config.basePrice,

        get baseFormatted() {
            return this.basePrice.toLocaleString('en-IN');
        },

        get selectedAddons() {
            return this.checkedKeys.map((key) => {
                const node = document.querySelector('[data-addon-key="' + key + '"]');
                if (!node) return null;

                const price = Number(node.dataset.addonPrice || 0);

                return {
                    key,
                    label: node.dataset.addonLabel,
                    price,
                    priceFormatted: price.toLocaleString('en-IN'),
                    billingLabel: node.dataset.addonBilling === 'one_time' ? 'one-time' : '/mo',
                    isMonthly: node.dataset.addonBilling !== 'one_time',
                };
            }).filter(Boolean);
        },

        get totalFormatted() {
            const addonsTotal = this.selectedAddons
                .filter((addon) => addon.isMonthly)
                .reduce((sum, addon) => sum + addon.price, 0);

            return (this.basePrice + addonsTotal).toLocaleString('en-IN');
        },
    };
}
</script>
