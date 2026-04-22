<x-layouts.super-admin>
    <x-slot:title>Add Gym — Super Admin | {{ config('app.name') }}</x-slot:title>
    <x-slot:header>Add Gym</x-slot:header>
    <x-slot:topbarTitle>Register New Gym</x-slot:topbarTitle>
    <x-slot:breadcrumb>Super Admin / <a href="{{ route('super-admin.gyms.index') }}" style="color:#0abf8e;text-decoration:none;">Gyms</a> / Add</x-slot:breadcrumb>

    <div style="max-width:720px;">
        <form method="POST" action="{{ route('super-admin.gyms.store') }}">
            @csrf

            {{-- Gym Info --}}
            <div style="background:#fff;border-radius:14px;border:1px solid #e2e8f0;overflow:hidden;margin-bottom:16px;">
                <div style="padding:18px 24px;border-bottom:1px solid #f1f5f9;display:flex;align-items:center;gap:10px;">
                    <div style="width:32px;height:32px;background:#ecfdf5;border-radius:8px;display:flex;align-items:center;justify-content:center;">
                        <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="#0abf8e" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 21h16.5M4.5 3h15M5.25 3v18m13.5-18v18M9 6.75h1.5m-1.5 3h1.5m-1.5 3h1.5m3-6H15m-1.5 3H15m-1.5 3H15M9 21v-3.375c0-.621.504-1.125 1.125-1.125h3.75c.621 0 1.125.504 1.125 1.125V21"/></svg>
                    </div>
                    <h3 style="font-size:15px;font-weight:700;color:#0f172a;margin:0;">Gym Information</h3>
                </div>
                <div style="padding:24px;display:flex;flex-direction:column;gap:16px;">
                    <div>
                        <label style="display:block;font-size:13px;font-weight:600;color:#374151;margin-bottom:6px;">Gym Name <span style="color:#ef4444;">*</span></label>
                        <input type="text" name="name" value="{{ old('name') }}" placeholder="Fitness Pro Gym"
                               style="width:100%;padding:9px 12px;border:1px solid {{ $errors->has('name') ? '#ef4444' : '#d1d5db' }};border-radius:8px;font-size:14px;color:#0f172a;outline:none;box-sizing:border-box;">
                        @error('name')<p style="color:#ef4444;font-size:12px;margin:4px 0 0;">{{ $message }}</p>@enderror
                    </div>
                    <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;">
                        <div>
                            <label style="display:block;font-size:13px;font-weight:600;color:#374151;margin-bottom:6px;">Phone</label>
                            <input type="text" name="phone" value="{{ old('phone') }}" placeholder="+91 98765 43210"
                                   style="width:100%;padding:9px 12px;border:1px solid #d1d5db;border-radius:8px;font-size:14px;color:#0f172a;outline:none;box-sizing:border-box;">
                        </div>
                        <div>
                            <label style="display:block;font-size:13px;font-weight:600;color:#374151;margin-bottom:6px;">Email</label>
                            <input type="email" name="email" value="{{ old('email') }}" placeholder="gym@example.com"
                                   style="width:100%;padding:9px 12px;border:1px solid #d1d5db;border-radius:8px;font-size:14px;color:#0f172a;outline:none;box-sizing:border-box;">
                        </div>
                    </div>
                    <div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:16px;">
                        <div>
                            <label style="display:block;font-size:13px;font-weight:600;color:#374151;margin-bottom:6px;">City</label>
                            <input type="text" name="city" value="{{ old('city') }}" placeholder="Mumbai"
                                   style="width:100%;padding:9px 12px;border:1px solid #d1d5db;border-radius:8px;font-size:14px;color:#0f172a;outline:none;box-sizing:border-box;">
                        </div>
                        <div>
                            <label style="display:block;font-size:13px;font-weight:600;color:#374151;margin-bottom:6px;">Country</label>
                            <input type="text" name="country" value="{{ old('country', 'India') }}"
                                   style="width:100%;padding:9px 12px;border:1px solid #d1d5db;border-radius:8px;font-size:14px;color:#0f172a;outline:none;box-sizing:border-box;">
                        </div>
                        <div>
                            <label style="display:block;font-size:13px;font-weight:600;color:#374151;margin-bottom:6px;">Currency</label>
                            <select name="currency" style="width:100%;padding:9px 12px;border:1px solid #d1d5db;border-radius:8px;font-size:14px;color:#0f172a;outline:none;box-sizing:border-box;background:#fff;">
                                <option value="INR" {{ old('currency','INR')=='INR'?'selected':'' }}>INR (₹)</option>
                                <option value="USD" {{ old('currency')=='USD'?'selected':'' }}>USD ($)</option>
                                <option value="EUR" {{ old('currency')=='EUR'?'selected':'' }}>EUR (€)</option>
                                <option value="GBP" {{ old('currency')=='GBP'?'selected':'' }}>GBP (£)</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Owner Info --}}
            <div style="background:#fff;border-radius:14px;border:1px solid #e2e8f0;overflow:hidden;margin-bottom:16px;">
                <div style="padding:18px 24px;border-bottom:1px solid #f1f5f9;display:flex;align-items:center;gap:10px;">
                    <div style="width:32px;height:32px;background:#eff6ff;border-radius:8px;display:flex;align-items:center;justify-content:center;">
                        <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="#3b82f6" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z"/></svg>
                    </div>
                    <h3 style="font-size:15px;font-weight:700;color:#0f172a;margin:0;">Gym Owner Account</h3>
                </div>
                <div style="padding:24px;display:flex;flex-direction:column;gap:16px;">
                    <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;">
                        <div>
                            <label style="display:block;font-size:13px;font-weight:600;color:#374151;margin-bottom:6px;">Owner Full Name <span style="color:#ef4444;">*</span></label>
                            <input type="text" name="owner_name" value="{{ old('owner_name') }}" placeholder="Rahul Sharma"
                                   style="width:100%;padding:9px 12px;border:1px solid {{ $errors->has('owner_name') ? '#ef4444' : '#d1d5db' }};border-radius:8px;font-size:14px;color:#0f172a;outline:none;box-sizing:border-box;">
                            @error('owner_name')<p style="color:#ef4444;font-size:12px;margin:4px 0 0;">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label style="display:block;font-size:13px;font-weight:600;color:#374151;margin-bottom:6px;">Owner Email <span style="color:#ef4444;">*</span></label>
                            <input type="email" name="owner_email" value="{{ old('owner_email') }}" placeholder="owner@fitnesspro.com"
                                   style="width:100%;padding:9px 12px;border:1px solid {{ $errors->has('owner_email') ? '#ef4444' : '#d1d5db' }};border-radius:8px;font-size:14px;color:#0f172a;outline:none;box-sizing:border-box;">
                            @error('owner_email')<p style="color:#ef4444;font-size:12px;margin:4px 0 0;">{{ $message }}</p>@enderror
                        </div>
                    </div>
                    <div>
                        <label style="display:block;font-size:13px;font-weight:600;color:#374151;margin-bottom:6px;">Temporary Password <span style="color:#ef4444;">*</span></label>
                        <input type="password" name="owner_password" placeholder="Min. 8 characters"
                               style="width:100%;padding:9px 12px;border:1px solid {{ $errors->has('owner_password') ? '#ef4444' : '#d1d5db' }};border-radius:8px;font-size:14px;color:#0f172a;outline:none;box-sizing:border-box;">
                        @error('owner_password')<p style="color:#ef4444;font-size:12px;margin:4px 0 0;">{{ $message }}</p>@enderror
                        <p style="font-size:12px;color:#94a3b8;margin:4px 0 0;">Share this with the gym owner. They should change it after first login.</p>
                    </div>
                </div>
            </div>

            {{-- Subscription --}}
            <div style="background:#fff;border-radius:14px;border:1px solid #e2e8f0;overflow:hidden;margin-bottom:20px;">
                <div style="padding:18px 24px;border-bottom:1px solid #f1f5f9;display:flex;align-items:center;gap:10px;">
                    <div style="width:32px;height:32px;background:#fdf4ff;border-radius:8px;display:flex;align-items:center;justify-content:center;">
                        <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="#a855f7" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v12m-3-2.818.879.659c1.171.879 3.07.879 4.242 0 1.172-.879 1.172-2.303 0-3.182C13.536 12.219 12.768 12 12 12c-.725 0-1.45-.22-2.003-.659-1.106-.879-1.106-2.303 0-3.182s2.9-.879 4.006 0l.415.33M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                    <h3 style="font-size:15px;font-weight:700;color:#0f172a;margin:0;">Subscription Plan</h3>
                </div>
                <div style="padding:24px;display:flex;flex-direction:column;gap:16px;">
                    <div>
                        <label style="display:block;font-size:13px;font-weight:600;color:#374151;margin-bottom:10px;">Select Plan <span style="color:#ef4444;">*</span></label>
                        <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(180px,1fr));gap:10px;">
                            @foreach($plans as $plan)
                            @php
                                $planColorMap = ['basic'=>'#3b82f6','pro'=>'#0abf8e','enterprise'=>'#a855f7'];
                                $pc = $planColorMap[$plan->name] ?? '#64748b';
                            @endphp
                            <label style="cursor:pointer;">
                                <input type="radio" name="plan_id" value="{{ $plan->id }}" {{ old('plan_id') == $plan->id ? 'checked' : '' }} style="display:none;" class="plan-radio">
                                <div class="plan-card-opt" data-id="{{ $plan->id }}" style="border:2px solid #e2e8f0;border-radius:10px;padding:14px;text-align:center;transition:all .15s;">
                                    <div style="font-size:14px;font-weight:700;color:#0f172a;">{{ $plan->display_name }}</div>
                                    <div style="font-size:18px;font-weight:800;color:{{ $pc }};margin:6px 0;">₹{{ number_format($plan->monthly_price) }}</div>
                                    <div style="font-size:11px;color:#94a3b8;">/month</div>
                                </div>
                            </label>
                            @endforeach
                        </div>
                        @error('plan_id')<p style="color:#ef4444;font-size:12px;margin:4px 0 0;">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label style="display:block;font-size:13px;font-weight:600;color:#374151;margin-bottom:6px;">Billing Cycle <span style="color:#ef4444;">*</span></label>
                        <div style="display:flex;gap:12px;">
                            <label style="display:flex;align-items:center;gap:8px;padding:9px 16px;border:1px solid #d1d5db;border-radius:8px;cursor:pointer;">
                                <input type="radio" name="billing_cycle" value="monthly" {{ old('billing_cycle','monthly')=='monthly'?'checked':'' }} style="accent-color:#0abf8e;">
                                <span style="font-size:14px;color:#374151;font-weight:500;">Monthly</span>
                            </label>
                            <label style="display:flex;align-items:center;gap:8px;padding:9px 16px;border:1px solid #d1d5db;border-radius:8px;cursor:pointer;">
                                <input type="radio" name="billing_cycle" value="annual" {{ old('billing_cycle')=='annual'?'checked':'' }} style="accent-color:#0abf8e;">
                                <span style="font-size:14px;color:#374151;font-weight:500;">Annual <span style="font-size:11px;color:#0abf8e;font-weight:600;">Save ~17%</span></span>
                            </label>
                        </div>
                    </div>
                </div>
            </div>

            <div style="display:flex;gap:10px;">
                <button type="submit" style="padding:10px 24px;background:#0abf8e;color:#fff;border:none;border-radius:8px;font-size:14px;font-weight:600;cursor:pointer;">Create Gym & Owner</button>
                <a href="{{ route('super-admin.gyms.index') }}" style="padding:10px 24px;background:#f1f5f9;color:#374151;border-radius:8px;text-decoration:none;font-size:14px;font-weight:600;">Cancel</a>
            </div>
        </form>
    </div>

@push('scripts')
<script>
document.querySelectorAll('.plan-radio').forEach(radio => {
    radio.addEventListener('change', function() {
        document.querySelectorAll('.plan-card-opt').forEach(c => {
            c.style.borderColor = '#e2e8f0';
            c.style.background = '#fff';
        });
        if (this.checked) {
            const card = document.querySelector('.plan-card-opt[data-id="' + this.value + '"]');
            if (card) { card.style.borderColor = '#0abf8e'; card.style.background = '#f0fdf4'; }
        }
    });
});
// Init selected
const checked = document.querySelector('.plan-radio:checked');
if (checked) { const card = document.querySelector('.plan-card-opt[data-id="' + checked.value + '"]'); if (card) { card.style.borderColor = '#0abf8e'; card.style.background = '#f0fdf4'; } }
</script>
@endpush

</x-layouts.super-admin>
