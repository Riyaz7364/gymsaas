<x-layouts.signup :currentStep="2">
    <x-slot:title>Your Gym — {{ config('app.name') }}</x-slot:title>

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

        <form method="POST" action="{{ route('signup.step2.post') }}" @submit="loading = true">
            @csrf

            {{-- Gym Name --}}
            <div class="su-form-group">
                <label class="su-label" for="gym_name">Gym Name</label>
                <div class="su-input-wrap">
                    <span class="su-input-icon">
                        <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 21h16.5M4.5 3h15M5.25 3v18m13.5-18v18M9 6.75h1.5m-1.5 3h1.5m-1.5 3h1.5m3-6H15m-1.5 3H15m-1.5 3H15M9 21v-3.375c0-.621.504-1.125 1.125-1.125h3.75c.621 0 1.125.504 1.125 1.125V21"/></svg>
                    </span>
                    <input
                        type="text"
                        id="gym_name"
                        name="gym_name"
                        class="su-input"
                        value="{{ old('gym_name') }}"
                        placeholder="e.g. PowerFit Gym"
                        autofocus
                        required>
                </div>
                @error('gym_name')<p class="su-error">{{ $message }}</p>@enderror
            </div>

            {{-- Address --}}
            <div class="su-form-group">
                <label class="su-label" for="address">Address <span style="font-weight:400; color:#9ca3af;">(optional)</span></label>
                <div class="su-input-wrap">
                    <span class="su-input-icon">
                        <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1115 0z"/></svg>
                    </span>
                    <input
                        type="text"
                        id="address"
                        name="address"
                        class="su-input"
                        value="{{ old('address') }}"
                        placeholder="Street address">
                </div>
                @error('address')<p class="su-error">{{ $message }}</p>@enderror
            </div>

            {{-- City + State --}}
            <div style="display:grid; grid-template-columns:1fr 1fr; gap:16px;">
                <div class="su-form-group" style="margin-bottom:0;">
                    <label class="su-label" for="city">City</label>
                    <div class="su-input-wrap">
                        <input
                            type="text"
                            id="city"
                            name="city"
                            class="su-input no-icon"
                            value="{{ old('city') }}"
                            placeholder="e.g. Mumbai"
                            required>
                    </div>
                    @error('city')<p class="su-error">{{ $message }}</p>@enderror
                </div>
                <div class="su-form-group" style="margin-bottom:0;">
                    <label class="su-label" for="state">State</label>
                    <div class="su-input-wrap">
                        <input
                            type="text"
                            id="state"
                            name="state"
                            class="su-input no-icon"
                            value="{{ old('state') }}"
                            placeholder="e.g. Maharashtra"
                            required>
                    </div>
                    @error('state')<p class="su-error">{{ $message }}</p>@enderror
                </div>
            </div>

            {{-- Country --}}
            <div class="su-form-group" style="margin-top:16px;">
                <label class="su-label" for="country">Country</label>
                <div class="su-input-wrap">
                    <select id="country" name="country" class="su-input no-icon" required>
                        @php $countries = ['India', 'United States', 'United Kingdom', 'Canada', 'Australia', 'UAE', 'Singapore', 'Other']; @endphp
                        <option value="">Select country</option>
                        @foreach($countries as $c)
                            <option value="{{ $c }}" {{ old('country', 'India') === $c ? 'selected' : '' }}>{{ $c }}</option>
                        @endforeach
                    </select>
                </div>
                @error('country')<p class="su-error">{{ $message }}</p>@enderror
            </div>

            {{-- Currency + Timezone --}}
            <div style="display:grid; grid-template-columns:1fr 1fr; gap:16px;">
                <div class="su-form-group" style="margin-bottom:0;">
                    <label class="su-label" for="currency">Currency</label>
                    <div class="su-input-wrap">
                        <select id="currency" name="currency" class="su-input no-icon" required>
                            <option value="INR" {{ old('currency', 'INR') === 'INR' ? 'selected' : '' }}>₹ INR</option>
                            <option value="USD" {{ old('currency') === 'USD' ? 'selected' : '' }}>$ USD</option>
                            <option value="GBP" {{ old('currency') === 'GBP' ? 'selected' : '' }}>£ GBP</option>
                            <option value="AED" {{ old('currency') === 'AED' ? 'selected' : '' }}>AED</option>
                            <option value="SGD" {{ old('currency') === 'SGD' ? 'selected' : '' }}>SGD</option>
                        </select>
                    </div>
                    @error('currency')<p class="su-error">{{ $message }}</p>@enderror
                </div>
                <div class="su-form-group" style="margin-bottom:0;">
                    <label class="su-label" for="timezone">Timezone</label>
                    <div class="su-input-wrap">
                        <select id="timezone" name="timezone" class="su-input no-icon" required>
                            <option value="Asia/Kolkata" {{ old('timezone', 'Asia/Kolkata') === 'Asia/Kolkata' ? 'selected' : '' }}>Asia/Kolkata (IST)</option>
                            <option value="America/New_York" {{ old('timezone') === 'America/New_York' ? 'selected' : '' }}>America/New_York</option>
                            <option value="America/Los_Angeles" {{ old('timezone') === 'America/Los_Angeles' ? 'selected' : '' }}>America/Los_Angeles</option>
                            <option value="Europe/London" {{ old('timezone') === 'Europe/London' ? 'selected' : '' }}>Europe/London</option>
                            <option value="Asia/Dubai" {{ old('timezone') === 'Asia/Dubai' ? 'selected' : '' }}>Asia/Dubai</option>
                            <option value="Asia/Singapore" {{ old('timezone') === 'Asia/Singapore' ? 'selected' : '' }}>Asia/Singapore</option>
                        </select>
                    </div>
                    @error('timezone')<p class="su-error">{{ $message }}</p>@enderror
                </div>
            </div>

            <button type="submit" style="place-content:space-between !important;" class="my-2 text-white bg-[#3b5998] hover:bg-[#3b5998]/90 focus:ring-4 focus:outline-none focus:ring-[#3b5998]/50 box-border border border-transparent font-medium leading-5 rounded-base text-sm px-4 py-2.5 text-center inline-flex items-center dark:focus:ring-[#3b5998]/55 w-full rounded" :disabled="loading">
                <span x-show="!loading">Continue</span>
                <span x-show="loading" style="display:none;">Processing…</span>
                <svg class="w-4 h-4 me-1.5" x-show="!loading" width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3"/></svg>
            </button>
        </form>
    </div>
</x-layouts.signup>
