<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'Super Admin — ' . config('app.name') }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('styles')
</head>
<body style="background:#f0f2f5;">

{{-- ── Super Admin Sidebar ─────────────────────────────────────────── --}}
<aside style="position:fixed;top:0;left:0;bottom:0;width:240px;background:#0f172a;overflow-y:auto;z-index:200;display:flex;flex-direction:column;" id="sa-sidebar">

    {{-- Logo / Brand --}}
    <div style="padding:20px 18px 16px;border-bottom:1px solid rgba(255,255,255,.08);">
        <div style="display:flex;align-items:center;gap:10px;text-decoration:none;">
            <div style="width:34px;height:34px;background:linear-gradient(135deg,#0abf8e,#0891b2);border-radius:8px;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                <svg width="18" height="18" viewBox="0 0 32 32" fill="none"><path d="M8 20V14l8-6 8 6v6" stroke="#fff" stroke-width="2.5" stroke-linejoin="round"/><rect x="13" y="18" width="6" height="6" rx="1" fill="#fff"/></svg>
            </div>
            <div>
                <div style="font-size:14px;font-weight:700;color:#fff;line-height:1.2;">GymHub</div>
                <div style="font-size:10px;font-weight:600;color:#0abf8e;letter-spacing:.08em;text-transform:uppercase;">Super Admin</div>
            </div>
        </div>
    </div>

    {{-- Navigation --}}
    <nav style="flex:1;padding:12px 0 24px;">

        <div style="font-size:10px;font-weight:600;letter-spacing:.1em;color:rgba(255,255,255,.35);text-transform:uppercase;padding:12px 18px 6px;">Overview</div>

        <a href="{{ route('super-admin.dashboard') }}"
           style="display:flex;align-items:center;gap:10px;padding:9px 18px;font-size:13.5px;font-weight:500;color:{{ request()->routeIs('super-admin.dashboard') ? '#fff' : 'rgba(255,255,255,.65)' }};text-decoration:none;background:{{ request()->routeIs('super-admin.dashboard') ? 'rgba(10,191,142,.15)' : 'transparent' }};border-left:3px solid {{ request()->routeIs('super-admin.dashboard') ? '#0abf8e' : 'transparent' }};transition:all .15s;">
            <svg width="17" height="17" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 013 19.875v-6.75zM9.75 8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V8.625zM16.5 4.125c0-.621.504-1.125 1.125-1.125h2.25C20.496 3 21 3.504 21 4.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V4.125z"/></svg>
            Dashboard
        </a>

        <div style="font-size:10px;font-weight:600;letter-spacing:.1em;color:rgba(255,255,255,.35);text-transform:uppercase;padding:16px 18px 6px;">Platform</div>

        <a href="{{ route('super-admin.gyms.index') }}"
           style="display:flex;align-items:center;gap:10px;padding:9px 18px;font-size:13.5px;font-weight:500;color:{{ request()->routeIs('super-admin.gyms.*') ? '#fff' : 'rgba(255,255,255,.65)' }};text-decoration:none;background:{{ request()->routeIs('super-admin.gyms.*') ? 'rgba(10,191,142,.15)' : 'transparent' }};border-left:3px solid {{ request()->routeIs('super-admin.gyms.*') ? '#0abf8e' : 'transparent' }};transition:all .15s;">
            <svg width="17" height="17" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 21h16.5M4.5 3h15M5.25 3v18m13.5-18v18M9 6.75h1.5m-1.5 3h1.5m-1.5 3h1.5m3-6H15m-1.5 3H15m-1.5 3H15M9 21v-3.375c0-.621.504-1.125 1.125-1.125h3.75c.621 0 1.125.504 1.125 1.125V21"/></svg>
            All Gyms
        </a>

        <a href="{{ route('super-admin.gyms.create') }}"
           style="display:flex;align-items:center;gap:10px;padding:9px 18px;font-size:13.5px;font-weight:500;color:rgba(255,255,255,.65);text-decoration:none;background:transparent;border-left:3px solid transparent;transition:all .15s;"
           onmouseover="this.style.color='#fff';this.style.background='rgba(255,255,255,.05)'"
           onmouseout="this.style.color='rgba(255,255,255,.65)';this.style.background='transparent'">
            <svg width="17" height="17" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/></svg>
            Add New Gym
        </a>

        <div style="font-size:10px;font-weight:600;letter-spacing:.1em;color:rgba(255,255,255,.35);text-transform:uppercase;padding:16px 18px 6px;">Subscriptions</div>

        <a href="{{ route('super-admin.pricing.index') }}"
           style="display:flex;align-items:center;gap:10px;padding:9px 18px;font-size:13.5px;font-weight:500;color:{{ request()->routeIs('super-admin.pricing.*') ? '#fff' : 'rgba(255,255,255,.65)' }};text-decoration:none;background:{{ request()->routeIs('super-admin.pricing.*') ? 'rgba(10,191,142,.15)' : 'transparent' }};border-left:3px solid {{ request()->routeIs('super-admin.pricing.*') ? '#0abf8e' : 'transparent' }};transition:all .15s;">
            <svg width="17" height="17" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75m-3-7.036A11.959 11.959 0 013.598 6 11.99 11.99 0 003 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285z"/></svg>
            Pricing Plans
        </a>

        {{-- Future: All Subscriptions list page --}}
        <a href="{{ route('super-admin.gyms.index') }}"
           style="display:flex;align-items:center;gap:10px;padding:9px 18px;font-size:13.5px;font-weight:500;color:rgba(255,255,255,.65);text-decoration:none;background:transparent;border-left:3px solid transparent;transition:all .15s;"
           onmouseover="this.style.color='#fff';this.style.background='rgba(255,255,255,.05)'"
           onmouseout="this.style.color='rgba(255,255,255,.65)';this.style.background='transparent'">
            <svg width="17" height="17" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 8.25h19.5M2.25 9h19.5m-16.5 5.25h6m-6 2.25h3m-3.75 3h15a2.25 2.25 0 002.25-2.25V6.75A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25v10.5A2.25 2.25 0 004.5 19.5z"/></svg>
            Manage Subscriptions
        </a>

        <div style="font-size:10px;font-weight:600;letter-spacing:.1em;color:rgba(255,255,255,.35);text-transform:uppercase;padding:16px 18px 6px;">System</div>

        <a href="{{ route('super-admin.settings') }}"
           style="display:flex;align-items:center;gap:10px;padding:9px 18px;font-size:13.5px;font-weight:500;color:{{ request()->routeIs('super-admin.settings') ? '#fff' : 'rgba(255,255,255,.65)' }};text-decoration:none;background:{{ request()->routeIs('super-admin.settings') ? 'rgba(10,191,142,.15)' : 'transparent' }};border-left:3px solid {{ request()->routeIs('super-admin.settings') ? '#0abf8e' : 'transparent' }};transition:all .15s;">
            <svg width="17" height="17" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M9.594 3.94c.09-.542.56-.94 1.11-.94h2.593c.55 0 1.02.398 1.11.94l.213 1.281c.063.374.313.686.645.87.074.04.147.083.22.127.324.196.72.257 1.075.124l1.217-.456a1.125 1.125 0 011.37.49l1.296 2.247a1.125 1.125 0 01-.26 1.431l-1.003.827c-.293.24-.438.613-.431.992a6.759 6.759 0 010 .255c-.007.378.138.75.43.99l1.005.828c.424.35.534.954.26 1.43l-1.298 2.247a1.125 1.125 0 01-1.369.491l-1.217-.456c-.355-.133-.75-.072-1.076.124a6.57 6.57 0 01-.22.128c-.331.183-.581.495-.644.869l-.213 1.28c-.09.543-.56.941-1.11.941h-2.594c-.55 0-1.02-.398-1.11-.94l-.213-1.281c-.062-.374-.312-.686-.644-.87a6.52 6.52 0 01-.22-.127c-.325-.196-.72-.257-1.076-.124l-1.217.456a1.125 1.125 0 01-1.369-.49l-1.297-2.247a1.125 1.125 0 01.26-1.431l1.004-.827c.292-.24.437-.613.43-.992a6.932 6.932 0 010-.255c.007-.378-.138-.75-.43-.99l-1.004-.828a1.125 1.125 0 01-.26-1.43l1.297-2.247a1.125 1.125 0 011.37-.491l1.216.456c.356.133.751.072 1.076-.124.072-.044.146-.087.22-.128.332-.183.582-.495.644-.869l.214-1.281z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
            Platform Settings
        </a>

    </nav>

    {{-- User footer --}}
    <div style="padding:14px 18px;border-top:1px solid rgba(255,255,255,.08);">
        <div style="display:flex;align-items:center;gap:10px;">
            <div style="width:32px;height:32px;border-radius:50%;background:linear-gradient(135deg,#0abf8e,#0891b2);display:flex;align-items:center;justify-content:center;font-size:13px;font-weight:700;color:#fff;flex-shrink:0;">
                {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
            </div>
            <div style="flex:1;min-width:0;">
                <div style="font-size:13px;font-weight:600;color:#fff;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">{{ auth()->user()->name }}</div>
                <div style="font-size:11px;color:rgba(255,255,255,.4);">Super Admin</div>
            </div>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" title="Logout" style="background:none;border:none;cursor:pointer;color:rgba(255,255,255,.4);padding:4px;border-radius:4px;display:flex;"
                    onmouseover="this.style.color='#ef4444'" onmouseout="this.style.color='rgba(255,255,255,.4)'">
                    <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15m3 0l3-3m0 0l-3-3m3 3H9"/></svg>
                </button>
            </form>
        </div>
    </div>
</aside>

{{-- ── Topbar ──────────────────────────────────────────────────────────── --}}
<header style="position:fixed;top:0;left:240px;right:0;height:60px;background:#fff;border-bottom:1px solid #e2e8f0;display:flex;align-items:center;padding:0 24px;gap:12px;z-index:100;">
    <div style="flex:1;">
        @isset($topbarTitle)
            <span style="font-size:14px;font-weight:600;color:#1e293b;">{{ $topbarTitle }}</span>
        @endisset
    </div>
    <div style="display:flex;align-items:center;gap:8px;">
        <span style="font-size:12px;font-weight:600;background:#fef3c7;color:#92400e;padding:3px 10px;border-radius:20px;">SUPER ADMIN</span>
        <span style="font-size:13px;color:#64748b;">{{ auth()->user()->email }}</span>
    </div>
</header>

{{-- ── Main Content ────────────────────────────────────────────────────── --}}
<main style="margin-left:240px;padding-top:60px;min-height:100vh;">

    @isset($header)
    <div style="background:#fff;border-bottom:1px solid #e2e8f0;padding:14px 24px;display:flex;align-items:center;justify-content:space-between;margin-bottom:24px;">
        <h1 style="font-size:16px;font-weight:600;margin:0;">{{ $header }}</h1>
        @isset($breadcrumb)
        <nav style="font-size:13px;color:#64748b;">{{ $breadcrumb }}</nav>
        @endisset
    </div>
    @endisset

    @if(session('success'))
    <div style="margin:0 24px 16px;">
        <div class="gh-alert gh-alert-success">
            <svg width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" style="flex-shrink:0;"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            {{ session('success') }}
        </div>
    </div>
    @endif
    @if(session('error'))
    <div style="margin:0 24px 16px;">
        <div class="gh-alert gh-alert-danger">{{ session('error') }}</div>
    </div>
    @endif

    <div style="padding:0 24px 24px;">
        {{ $slot }}
    </div>
</main>

@stack('scripts')
</body>
</html>
