<x-layouts.auth>
    <x-slot:title>Forgot Password — {{ config('app.name') }}</x-slot:title>

    <div class="gh-auth-card">
        <div class="gh-auth-logo">
            <svg width="40" height="40" viewBox="0 0 32 32" fill="none">
                <rect width="32" height="32" rx="8" fill="#0abf8e"/>
                <path d="M8 20V14l8-6 8 6v6" stroke="#fff" stroke-width="2" stroke-linejoin="round"/>
                <rect x="13" y="18" width="6" height="6" rx="1" fill="#fff"/>
            </svg>
            <span style="font-size:24px; font-weight:700; color:#111827; margin-left:10px;">GymHub</span>
        </div>

        <h1 style="font-size:22px; font-weight:700; color:#111827; margin:0 0 4px;">Forgot Password?</h1>
        <p style="font-size:14px; color:#6b7280; margin:0 0 24px;">Enter your email and we'll send you a reset link.</p>

        @if (session('status'))
        <div class="gh-alert gh-alert-success" style="margin-bottom:16px;">
            {{ session('status') }}
        </div>
        @endif

        @if ($errors->any())
        <div class="gh-alert gh-alert-danger" style="margin-bottom:16px;">
            {{ $errors->first() }}
        </div>
        @endif

        <form method="POST" action="{{ route('password.email') }}">
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
                    required>
            </div>

            <button type="submit" class="gh-btn gh-btn-primary" style="width:100%;">
                Send Reset Link
            </button>
        </form>

        <p style="text-align:center; font-size:13px; color:#9ca3af; margin-top:20px;">
            <a href="{{ route('login') }}" style="color:#0abf8e; text-decoration:none;">← Back to Login</a>
        </p>
    </div>
</x-layouts.auth>
