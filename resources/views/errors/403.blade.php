<x-layouts.app>
    <x-slot:title>Feature Not Available | {{ config('app.name') }}</x-slot:title>

    <div style="display:flex;flex-direction:column;align-items:center;justify-content:center;min-height:60vh;text-align:center;padding:40px 20px;">

        {{-- Lock icon --}}
        <div style="width:80px;height:80px;background:#fef9c3;border-radius:50%;display:flex;align-items:center;justify-content:center;margin-bottom:24px;">
            <svg width="36" height="36" fill="none" viewBox="0 0 24 24" stroke="#ca8a04" stroke-width="1.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 10-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25H6.75a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25z"/>
            </svg>
        </div>

        <h1 style="font-size:22px;font-weight:700;color:#0f172a;margin:0 0 10px;">Feature Not Available</h1>
        <p style="font-size:15px;color:#64748b;margin:0 0 6px;max-width:420px;">{{ $exception->getMessage() ?: 'This feature is not included in your current subscription plan.' }}</p>
        <p style="font-size:13px;color:#94a3b8;margin:0 0 32px;">Upgrade your plan to unlock premium modules.</p>

        <div style="display:flex;gap:12px;flex-wrap:wrap;justify-content:center;">
            <a href="{{ url()->previous() }}"
               style="padding:10px 22px;background:#f1f5f9;color:#374151;border-radius:8px;text-decoration:none;font-size:14px;font-weight:600;">
                ← Go Back
            </a>
            <a href="{{ route('settings.index') }}"
               style="padding:10px 22px;background:#0abf8e;color:#fff;border-radius:8px;text-decoration:none;font-size:14px;font-weight:600;">
                View Plans &amp; Upgrade
            </a>
        </div>

    </div>
</x-layouts.app>
