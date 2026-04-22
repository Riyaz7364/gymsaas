<x-layouts.super-admin>
    <x-slot:title>Platform Settings — Super Admin | {{ config('app.name') }}</x-slot:title>
    <x-slot:header>Platform Settings</x-slot:header>
    <x-slot:topbarTitle>Platform Settings</x-slot:topbarTitle>
    <x-slot:breadcrumb>Super Admin / Settings</x-slot:breadcrumb>

    @if(session('success'))
        <div class="gh-alert success" style="margin-bottom:20px; padding:12px 18px; background:#dcfce7; color:#166534; border-radius:8px; border-left:4px solid #22c55e; font-size:14px; font-weight:500;">
            ✅ {{ session('success') }}
        </div>
    @endif

    <form method="POST" action="{{ route('super-admin.settings.update') }}">
        @csrf

        {{-- ── Signup → Start Type Settings ─────────────────────────── --}}
        <div class="gh-card" style="margin-bottom:24px;">
            <div class="gh-card-header" style="padding:18px 24px; border-bottom:1px solid #f3f4f6;">
                <h3 style="font-size:16px; font-weight:600; color:#111827; margin:0;">
                    🚀 Signup — Start Type Settings
                </h3>
                <p style="font-size:13px; color:#6b7280; margin:4px 0 0;">
                    Controls what options gym owners see on Step 3 of the signup flow.
                </p>
            </div>
            <div class="gh-card-body" style="padding:24px; display:grid; grid-template-columns:1fr 1fr; gap:24px;">

                {{-- Free Trial —— left column --}}
                <div style="background:#f9fafb; border-radius:10px; padding:20px; border:1px solid #e5e7eb;">
                    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:16px;">
                        <h4 style="font-size:14px; font-weight:600; color:#374151; margin:0;">🎁 Free Trial Card</h4>
                        <label style="display:flex; align-items:center; gap:8px; cursor:pointer; font-size:13px; color:#374151; user-select:none;">
                            <span>Enabled</span>
                            <input type="hidden" name="trial_enabled" value="0">
                            <input type="checkbox" name="trial_enabled" value="1"
                                   @checked($settings['trial_enabled'] ?? true)
                                   style="width:16px; height:16px; accent-color:#0abf8e; cursor:pointer;">
                        </label>
                    </div>

                    <div style="margin-bottom:14px;">
                        <label style="display:block; font-size:12px; font-weight:600; color:#6b7280; text-transform:uppercase; letter-spacing:.04em; margin-bottom:5px;">Card Title</label>
                        <input type="text" name="trial_card_title"
                               value="{{ old('trial_card_title', $settings['trial_card_title'] ?? 'Start Free Trial') }}"
                               style="width:100%; padding:8px 11px; border:1px solid #d1d5db; border-radius:7px; font-size:13px; color:#111827; box-sizing:border-box;">
                        @error('trial_card_title') <p style="color:#ef4444; font-size:12px; margin-top:4px;">{{ $message }}</p> @enderror
                    </div>

                    <div style="margin-bottom:14px;">
                        <label style="display:block; font-size:12px; font-weight:600; color:#6b7280; text-transform:uppercase; letter-spacing:.04em; margin-bottom:5px;">Card Sub-text <span style="font-weight:400; text-transform:none;">(use <code>{days}</code> for count)</span></label>
                        <input type="text" name="trial_card_desc"
                               value="{{ old('trial_card_desc', $settings['trial_card_desc'] ?? 'Try the platform free for {days} days') }}"
                               style="width:100%; padding:8px 11px; border:1px solid #d1d5db; border-radius:7px; font-size:13px; color:#111827; box-sizing:border-box;">
                        @error('trial_card_desc') <p style="color:#ef4444; font-size:12px; margin-top:4px;">{{ $message }}</p> @enderror
                    </div>

                    <div style="display:grid; grid-template-columns:1fr 1fr; gap:14px;">
                        <div>
                            <label style="display:block; font-size:12px; font-weight:600; color:#6b7280; text-transform:uppercase; letter-spacing:.04em; margin-bottom:5px;">Trial Days</label>
                            <input type="number" name="trial_days" min="1" max="365"
                                   value="{{ old('trial_days', $settings['trial_days'] ?? 30) }}"
                                   style="width:100%; padding:8px 11px; border:1px solid #d1d5db; border-radius:7px; font-size:13px; color:#111827; box-sizing:border-box;">
                            @error('trial_days') <p style="color:#ef4444; font-size:12px; margin-top:4px;">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label style="display:block; font-size:12px; font-weight:600; color:#6b7280; text-transform:uppercase; letter-spacing:.04em; margin-bottom:5px;">Badge Text</label>
                            <input type="text" name="trial_badge"
                                   value="{{ old('trial_badge', $settings['trial_badge'] ?? 'Recommended') }}"
                                   maxlength="40"
                                   style="width:100%; padding:8px 11px; border:1px solid #d1d5db; border-radius:7px; font-size:13px; color:#111827; box-sizing:border-box;">
                            @error('trial_badge') <p style="color:#ef4444; font-size:12px; margin-top:4px;">{{ $message }}</p> @enderror
                        </div>
                    </div>
                </div>

                {{-- Subscribe & Save —— right column --}}
                <div style="background:#f9fafb; border-radius:10px; padding:20px; border:1px solid #e5e7eb;">
                    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:16px;">
                        <h4 style="font-size:14px; font-weight:600; color:#374151; margin:0;">💳 Subscribe & Save Card</h4>
                        <label style="display:flex; align-items:center; gap:8px; cursor:pointer; font-size:13px; color:#374151; user-select:none;">
                            <span>Enabled</span>
                            <input type="hidden" name="subscribe_enabled" value="0">
                            <input type="checkbox" name="subscribe_enabled" value="1"
                                   @checked($settings['subscribe_enabled'] ?? true)
                                   style="width:16px; height:16px; accent-color:#0abf8e; cursor:pointer;">
                        </label>
                    </div>

                    <div style="margin-bottom:14px;">
                        <label style="display:block; font-size:12px; font-weight:600; color:#6b7280; text-transform:uppercase; letter-spacing:.04em; margin-bottom:5px;">Card Title</label>
                        <input type="text" name="sub_card_title"
                               value="{{ old('sub_card_title', $settings['sub_card_title'] ?? 'Subscribe & Save') }}"
                               style="width:100%; padding:8px 11px; border:1px solid #d1d5db; border-radius:7px; font-size:13px; color:#111827; box-sizing:border-box;">
                        @error('sub_card_title') <p style="color:#ef4444; font-size:12px; margin-top:4px;">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label style="display:block; font-size:12px; font-weight:600; color:#6b7280; text-transform:uppercase; letter-spacing:.04em; margin-bottom:5px;">Annual Discount %</label>
                        <div style="display:flex; align-items:center; gap:10px;">
                            <input type="number" name="annual_discount_pct" min="0" max="80"
                                   value="{{ old('annual_discount_pct', $settings['annual_discount_pct'] ?? 10) }}"
                                   style="flex:1; padding:8px 11px; border:1px solid #d1d5db; border-radius:7px; font-size:13px; color:#111827; box-sizing:border-box;">
                            <span style="font-size:20px; color:#374151; font-weight:600;">%</span>
                        </div>
                        <p style="font-size:12px; color:#9ca3af; margin-top:5px;">Shown as "SAVE X%" badge on the Subscribe card and annual billing option.</p>
                        @error('annual_discount_pct') <p style="color:#ef4444; font-size:12px; margin-top:4px;">{{ $message }}</p> @enderror
                    </div>
                </div>

            </div>

            <div style="padding:16px 24px; border-top:1px solid #f3f4f6; background:#f9fafb; border-radius:0 0 12px 12px; display:flex; align-items:center; gap:12px; flex-wrap:wrap;">
                <button type="submit"
                        style="padding:9px 22px; background:#0abf8e; color:#fff; border:none; border-radius:8px; font-size:13px; font-weight:600; cursor:pointer; transition:background .15s;"
                        onmouseover="this.style.background='#089e77'" onmouseout="this.style.background='#0abf8e'">
                    Save Settings
                </button>
                <span style="font-size:12px; color:#9ca3af;">Changes take effect immediately on the signup page.</span>
            </div>
        </div>
    </form>

    {{-- ── Preview hint ──────────────────────────────────────────── --}}
    <div class="gh-card">
        <div class="gh-card-body" style="padding:20px 24px; display:flex; align-items:center; gap:16px;">
            <div style="font-size:32px; flex-shrink:0;">👁️</div>
            <div>
                <div style="font-size:14px; font-weight:600; color:#111827; margin-bottom:3px;">Preview the signup flow</div>
                <div style="font-size:13px; color:#6b7280;">
                    Open <a href="{{ route('signup') }}" target="_blank" style="color:#0abf8e; text-decoration:none; font-weight:500;">the signup page</a>
                    in a new tab to see how the settings apply. You can go through all steps without committing any data.
                </div>
            </div>
        </div>
    </div>

</x-layouts.super-admin>
