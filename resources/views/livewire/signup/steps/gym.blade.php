<div class="signup-card wide" x-data="{ loading: false }">
    <h1 class="signup-card-title">Tell us about your gym</h1>
    <p class="signup-card-subtitle">We'll set up your gym profile with this information.</p>

    @if($errors->any())
        <div class="su-alert-error">
            <ul style="margin:0; padding-left:16px; list-style:disc;">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form wire:submit.prevent="saveGym" @submit="loading = true">
        <div class="su-form-group">
            <label class="su-label" for="gym_name">Gym Name</label>
            <div class="su-input-wrap">
                <span class="su-input-icon">
                    <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 21h16.5M4.5 3h15M5.25 3v18m13.5-18v18M9 6.75h1.5m-1.5 3h1.5m-1.5 3h1.5m3-6H15m-1.5 3H15m-1.5 3H15M9 21v-3.375c0-.621.504-1.125 1.125-1.125h3.75c.621 0 1.125.504 1.125 1.125V21"/></svg>
                </span>
                <input type="text" id="gym_name" wire:model.defer="gym_name" class="su-input" placeholder="e.g. PowerFit Gym" autofocus required>
            </div>
            @error('gym_name')<p class="su-error">{{ $message }}</p>@enderror
        </div>

        <div class="su-form-group">
            <label class="su-label" for="address">Address <span style="font-weight:400; color:#9ca3af;">(optional)</span></label>
            <div class="su-input-wrap">
                <span class="su-input-icon">
                    <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1115 0z"/></svg>
                </span>
                <input type="text" id="address" wire:model.defer="address" class="su-input" placeholder="Street address">
            </div>
            @error('address')<p class="su-error">{{ $message }}</p>@enderror
        </div>

        <div style="display:grid; grid-template-columns:1fr 1fr; gap:16px;">
            <div class="su-form-group" style="margin-bottom:0;">
                <label class="su-label" for="city">City</label>
                <div class="su-input-wrap">
                    <input type="text" id="city" wire:model.defer="city" class="su-input no-icon" placeholder="e.g. Mumbai" required>
                </div>
                @error('city')<p class="su-error">{{ $message }}</p>@enderror
            </div>
            <div class="su-form-group" style="margin-bottom:0;">
                <label class="su-label" for="state">State</label>
                <div class="su-input-wrap">
                    <input type="text" id="state" wire:model.defer="state" class="su-input no-icon" placeholder="e.g. Maharashtra" required>
                </div>
                @error('state')<p class="su-error">{{ $message }}</p>@enderror
            </div>
        </div>

        <div class="su-form-group" style="margin-top:16px;">
            <label class="su-label" for="country">Country</label>
            <div class="su-input-wrap">
                <select id="country" wire:model.defer="country" class="su-input no-icon" required>
                    @foreach(['India', 'United States', 'United Kingdom', 'Canada', 'Australia', 'UAE', 'Singapore', 'Other'] as $item)
                        <option value="{{ $item }}">{{ $item }}</option>
                    @endforeach
                </select>
            </div>
            @error('country')<p class="su-error">{{ $message }}</p>@enderror
        </div>

        <div style="display:grid; grid-template-columns:1fr 1fr; gap:16px;">
            <div class="su-form-group" style="margin-bottom:0;">
                <label class="su-label" for="currency">Currency</label>
                <div class="su-input-wrap">
                    <select id="currency" wire:model.defer="currency" class="su-input no-icon" required>
                        <option value="INR">INR</option>
                        <option value="USD">USD</option>
                        <option value="GBP">GBP</option>
                        <option value="AED">AED</option>
                        <option value="SGD">SGD</option>
                    </select>
                </div>
                @error('currency')<p class="su-error">{{ $message }}</p>@enderror
            </div>
            <div class="su-form-group" style="margin-bottom:0;">
                <label class="su-label" for="timezone">Timezone</label>
                <div class="su-input-wrap">
                    <select id="timezone" wire:model.defer="timezone" class="su-input no-icon" required>
                        <option value="Asia/Kolkata">Asia/Kolkata (IST)</option>
                        <option value="America/New_York">America/New_York</option>
                        <option value="America/Los_Angeles">America/Los_Angeles</option>
                        <option value="Europe/London">Europe/London</option>
                        <option value="Asia/Dubai">Asia/Dubai</option>
                        <option value="Asia/Singapore">Asia/Singapore</option>
                    </select>
                </div>
                @error('timezone')<p class="su-error">{{ $message }}</p>@enderror
            </div>
        </div>

        <div class="su-row-btns">
            <button type="button" wire:click="goTo('verify')" class="su-btn-ghost">Back</button>
            <button type="submit" class="su-btn su-btn-primary" :disabled="loading">
                <span x-show="!loading">Continue</span>
                <span x-show="loading" style="display:none;">Processing...</span>
            </button>
        </div>
    </form>
</div>
