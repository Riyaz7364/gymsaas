<div class="signup-card" x-data="{ loading: false }">
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

    <form wire:submit.prevent="saveAccount" @submit="loading = true">
        <div class="su-form-group">
            <label class="su-label" for="name">Full Name</label>
            <div class="su-input-wrap">
                <span class="su-input-icon">
                    <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z"/></svg>
                </span>
                <input type="text" id="name" wire:model.defer="name" class="su-input" placeholder="John Doe" autofocus required>
            </div>
            @error('name')<p class="su-error">{{ $message }}</p>@enderror
        </div>

        <div class="su-form-group">
            <label class="su-label" for="email">Email</label>
            <div class="su-input-wrap">
                <span class="su-input-icon">
                    <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 01-2.25 2.25h-15a2.25 2.25 0 01-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25m19.5 0v.243a2.25 2.25 0 01-1.07 1.916l-7.5 4.615a2.25 2.25 0 01-2.36 0L3.32 8.91a2.25 2.25 0 01-1.07-1.916V6.75"/></svg>
                </span>
                <input type="email" id="email" wire:model.defer="email" class="su-input" placeholder="you@example.com" required>
            </div>
            @error('email')<p class="su-error">{{ $message }}</p>@enderror
        </div>

        <div class="su-form-group">
            <label class="su-label" for="phone">Phone</label>
            <div class="phone-wrap">
                <div class="phone-prefix">
                    <span>+91</span>
                </div>
                <input type="tel" id="phone" wire:model.defer="phone" class="phone-input" placeholder="9876543210" maxlength="10" required>
            </div>
            @error('phone')<p class="su-error">{{ $message }}</p>@enderror
        </div>

        <div class="su-form-group">
            <label class="su-label" for="password">Password</label>
            <div class="su-input-wrap" x-data="{ show: false }">
                <span class="su-input-icon">
                    <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 10-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25H6.75a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25z"/></svg>
                </span>
                <input :type="show ? 'text' : 'password'" id="password" wire:model.defer="password" class="su-input" placeholder="Create a strong password" required>
                <button type="button" class="su-input-right" @click="show = !show" tabindex="-1">Show</button>
            </div>
            @error('password')<p class="su-error">{{ $message }}</p>@enderror
        </div>

        <div class="su-form-group">
            <label class="su-label" for="password_confirmation">Confirm Password</label>
            <div class="su-input-wrap" x-data="{ show: false }">
                <span class="su-input-icon">
                    <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 10-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25H6.75a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25z"/></svg>
                </span>
                <input :type="show ? 'text' : 'password'" id="password_confirmation" wire:model.defer="password_confirmation" class="su-input" placeholder="Confirm your password" required>
                <button type="button" class="su-input-right" @click="show = !show" tabindex="-1">Show</button>
            </div>
            @error('password_confirmation')<p class="su-error">{{ $message }}</p>@enderror
        </div>

        <div class="su-checkbox-wrap" style="margin-bottom:4px;">
            <input type="checkbox" id="terms" wire:model.defer="terms" class="su-checkbox" value="1">
            <label for="terms" class="su-checkbox-label">
                I agree to the <a href="#" class="su-link">Terms of Service</a> and <a href="#" class="su-link">Privacy Policy</a>
            </label>
        </div>
        @error('terms')<p class="su-error" style="margin-bottom:12px;">{{ $message }}</p>@enderror

        <div class="su-row-btns">
            <button type="submit" class="su-btn su-btn-primary" :disabled="loading">
                <span x-show="!loading">Continue</span>
                <span x-show="loading" style="display:none;">Processing...</span>
            </button>
        </div>
    </form>

    <p class="su-footer-text">
        Already have an account? <a href="{{ route('login') }}" class="su-link">Sign in</a>
    </p>
</div>
