<x-layouts.auth>
    <x-slot:title>Login — {{ config('app.name') }}</x-slot:title>

    <div class="gh-auth-card">
        {{-- Logo --}}
        <div class="gh-auth-logo">
            <svg width="40" height="40" viewBox="0 0 32 32" fill="none">
                <rect width="32" height="32" rx="8" fill="#0abf8e"/>
                <path d="M8 20V14l8-6 8 6v6" stroke="#fff" stroke-width="2" stroke-linejoin="round"/>
                <rect x="13" y="18" width="6" height="6" rx="1" fill="#fff"/>
            </svg>
            <span style="font-size:24px; font-weight:700; color:#111827; margin-left:10px;">GymHub</span>
        </div>

        <h1 style="font-size:22px; font-weight:700; color:#111827; margin:0 0 4px;">Welcome back!</h1>
        <p style="font-size:14px; color:#6b7280; margin:0 0 24px;">Sign in to your account to continue</p>

        @if ($errors->has('email') || session('status'))
        <div class="gh-alert {{ session('status') ? 'gh-alert-success' : 'gh-alert-danger' }}" style="margin-bottom:16px;">
            {{ session('status') ?? $errors->first('email') }}
        </div>
        @endif

        <form method="POST" action="{{ route('login') }}">
            @csrf

            <div class="gh-form-group">
                <label class="gh-label" for="email">Email Address</label>
                <input
                    type="email"
                    id="email"
                    name="email"
                    class="gh-input @error('email') border-red-400 @enderror"
                    value="{{ old('email') }}"
                    placeholder="your@email.com"
                    autofocus
                    autocomplete="email"
                    required>
            </div>

            <div class="gh-form-group">
                <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:4px;">
                    <label class="gh-label" for="password" style="margin:0;">Password</label>
                    <a href="{{ route('password.request') }}"
                       style="font-size:13px; color:#0abf8e; text-decoration:none;">
                        Forgot password?
                    </a>
                </div>
                <div x-data="{ show: false }" style="position:relative;">
                    <input
                        :type="show ? 'text' : 'password'"
                        id="password"
                        name="password"
                        class="gh-input @error('password') border-red-400 @enderror"
                        placeholder="••••••••"
                        autocomplete="current-password"
                        required>
                    <button type="button" @click="show = !show"
                            style="position:absolute; right:12px; top:50%; transform:translateY(-50%); background:none; border:none; cursor:pointer; color:#9ca3af;">
                        <svg x-show="!show" width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                        <svg x-show="show" width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5" style="display:none;"><path stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.223A10.477 10.477 0 001.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.45 10.45 0 0112 4.5c4.756 0 8.773 3.162 10.065 7.498a10.523 10.523 0 01-4.293 5.774M6.228 6.228L3 3m3.228 3.228l3.65 3.65m7.894 7.894L21 21m-3.228-3.228l-3.65-3.65m0 0a3 3 0 10-4.243-4.243m4.242 4.242L9.88 9.88"/></svg>
                    </button>
                </div>
            </div>

            <div style="display:flex; align-items:center; gap:8px; margin-bottom:20px;">
                <input type="checkbox" id="remember" name="remember"
                       style="width:16px; height:16px; accent-color:#0abf8e; cursor:pointer;">
                <label for="remember" style="font-size:14px; color:#374151; cursor:pointer;">Remember me</label>
            </div>

            <button type="submit" class="gh-btn gh-btn-primary" style="width:100%;">
                Sign In
            </button>
        </form>

        <p style="text-align:center; font-size:13px; color:#9ca3af; margin-top:24px;">
            &copy; {{ date('Y') }} GymHub. All rights reserved.
        </p>
    </div>
</x-layouts.auth>
