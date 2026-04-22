<x-layouts.signup :currentStep="1">
    <x-slot:title>Create Account — {{ config('app.name') }}</x-slot:title>

    <div class="signup-card" x-data="signupStep1()">
        <h1 class="signup-card-title">Create your account</h1>
        <p class="signup-card-subtitle">Start your free trial or subscribe today</p>

        @if($errors->any())
            <div class="su-alert-error">
                <ul style="margin:0; padding-left:16px; list-style:disc;">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('signup.step1.post') }}" @submit="loading = true">
            @csrf

            {{-- Full Name --}}
            <div class="su-form-group">
                <label class="su-label" for="name">Full Name</label>
                <div class="su-input-wrap">
                    <span class="su-input-icon">
                        <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z"/></svg>
                    </span>
                    <input
                        type="text"
                        id="name"
                        name="name"
                        class="su-input"
                        value="{{ old('name') }}"
                        placeholder="John Doe"
                        autofocus
                        required>
                </div>
                @error('name')<p class="su-error">{{ $message }}</p>@enderror
            </div>

            {{-- Email --}}
            <div class="su-form-group">
                <label class="su-label" for="email">Email</label>
                <div class="su-input-wrap">
                    <span class="su-input-icon">
                        <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 01-2.25 2.25h-15a2.25 2.25 0 01-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25m19.5 0v.243a2.25 2.25 0 01-1.07 1.916l-7.5 4.615a2.25 2.25 0 01-2.36 0L3.32 8.91a2.25 2.25 0 01-1.07-1.916V6.75"/></svg>
                    </span>
                    <input
                        type="email"
                        id="email"
                        name="email"
                        class="su-input"
                        value="{{ old('email') }}"
                        placeholder="you@example.com"
                        required>
                </div>
                @error('email')<p class="su-error">{{ $message }}</p>@enderror
            </div>

            {{-- Phone --}}
            <div class="su-form-group">
                <label class="su-label" for="phone">Phone</label>
                <div class="phone-wrap">
                    <div class="phone-prefix">
                        <span>🇮🇳</span>
                        <span>+91</span>
                    </div>
                    <input
                        type="tel"
                        id="phone"
                        name="phone"
                        class="phone-input"
                        value="{{ old('phone') }}"
                        placeholder="9876543210"
                        maxlength="10"
                        required>
                </div>
                @error('phone')<p class="su-error">{{ $message }}</p>@enderror
            </div>

            {{-- Password --}}
            <div class="su-form-group">
                <label class="su-label" for="password">Password</label>
                <div class="su-input-wrap" x-data="{ show: false }">
                    <span class="su-input-icon">
                        <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 10-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25H6.75a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25z"/></svg>
                    </span>
                    <input
                        :type="show ? 'text' : 'password'"
                        id="password"
                        name="password"
                        class="su-input"
                        placeholder="Create a strong password"
                        required>
                    <button type="button" class="su-input-right" @click="show = !show" tabindex="-1">
                        <svg x-show="!show" width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                        <svg x-show="show" width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8" style="display:none;"><path stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.223A10.477 10.477 0 001.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.45 10.45 0 0112 4.5c4.756 0 8.773 3.162 10.065 7.498a10.523 10.523 0 01-4.293 5.774M6.228 6.228L3 3m3.228 3.228l3.65 3.65m7.894 7.894L21 21m-3.228-3.228l-3.65-3.65m0 0a3 3 0 10-4.243-4.243m4.242 4.242L9.88 9.88"/></svg>
                    </button>
                </div>
                @error('password')<p class="su-error">{{ $message }}</p>@enderror
            </div>

            {{-- Confirm Password --}}
            <div class="su-form-group">
                <label class="su-label" for="password_confirmation">Confirm Password</label>
                <div class="su-input-wrap" x-data="{ show: false }">
                    <span class="su-input-icon">
                        <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 10-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25H6.75a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25z"/></svg>
                    </span>
                    <input
                        :type="show ? 'text' : 'password'"
                        id="password_confirmation"
                        name="password_confirmation"
                        class="su-input"
                        placeholder="Confirm your password"
                        required>
                    <button type="button" class="su-input-right" @click="show = !show" tabindex="-1">
                        <svg x-show="!show" width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                        <svg x-show="show" width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8" style="display:none;"><path stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.223A10.477 10.477 0 001.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.45 10.45 0 0112 4.5c4.756 0 8.773 3.162 10.065 7.498a10.523 10.523 0 01-4.293 5.774M6.228 6.228L3 3m3.228 3.228l3.65 3.65m7.894 7.894L21 21m-3.228-3.228l-3.65-3.65m0 0a3 3 0 10-4.243-4.243m4.242 4.242L9.88 9.88"/></svg>
                    </button>
                </div>
                @error('password_confirmation')<p class="su-error">{{ $message }}</p>@enderror
            </div>

            {{-- Terms --}}
            <div class="su-checkbox-wrap" style="margin-bottom:4px;">
                <input type="checkbox" id="terms" name="terms" class="su-checkbox" value="1" {{ old('terms') ? 'checked' : '' }}>
                <label for="terms" class="su-checkbox-label">
                    I agree to the <a href="#" class="su-link">Terms of Service</a> and <a href="#" class="su-link">Privacy Policy</a>
                </label>
            </div>
            @error('terms')<p class="su-error" style="margin-bottom:12px;">{{ $message }}</p>@enderror

            <button type="submit" style="place-content:space-between !important;" class="my-2 text-white bg-[#3b5998] hover:bg-[#3b5998]/90 focus:ring-4 focus:outline-none focus:ring-[#3b5998]/50 box-border border border-transparent font-medium leading-5 rounded-base text-sm px-4 py-2.5 text-center inline-flex items-center dark:focus:ring-[#3b5998]/55 w-full rounded" :disabled="loading">
                <span x-show="!loading">Continue</span>
                <span x-show="loading" style="display:none;">Processing…</span>
                <svg class="w-4 h-4 me-1.5" x-show="!loading" width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3"/></svg>
            </button>
        </form>

        <p class="su-footer-text">
            Already have an account? <a href="{{ route('login') }}" class="su-link">Sign in</a>
        </p>

        <div class="su-trust-badges">
            <div class="su-trust-badge">
                <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="#4f46e5" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
                Your data is secure and encrypted
            </div>
            <div class="su-trust-badge">
                <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="#4f46e5" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                Trusted by 500+ gyms across India
            </div>
        </div>
    </div>

    <script>
        function signupStep1() {
            return { loading: false };
        }
    </script>
</x-layouts.signup>
