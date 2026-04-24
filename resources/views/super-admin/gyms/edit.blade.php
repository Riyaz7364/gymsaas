<x-layouts.super-admin>
    <x-slot:title>Edit {{ $gym->name }} — Super Admin | {{ config('app.name') }}</x-slot:title>
    <x-slot:header>Edit Gym</x-slot:header>
    <x-slot:topbarTitle>Edit: {{ $gym->name }}</x-slot:topbarTitle>
    <x-slot:breadcrumb>Super Admin / <a href="{{ route('super-admin.gyms.index') }}" style="color:#0abf8e;text-decoration:none;">Gyms</a> / <a href="{{ route('super-admin.gyms.show', $gym) }}" style="color:#0abf8e;text-decoration:none;">{{ $gym->name }}</a> / Edit</x-slot:breadcrumb>

    <div style="max-width:700px;">
        <form method="POST" action="{{ route('super-admin.gyms.update', $gym) }}">
            @csrf @method('PUT')

            <div style="background:#fff;border-radius:14px;border:1px solid #e2e8f0;overflow:hidden;margin-bottom:16px;">
                <div style="padding:18px 24px;border-bottom:1px solid #f1f5f9;">
                    <h3 style="font-size:15px;font-weight:700;color:#0f172a;margin:0;">Gym Details</h3>
                </div>
                <div style="padding:24px;display:flex;flex-direction:column;gap:16px;">

                    <div>
                        <label style="display:block;font-size:13px;font-weight:600;color:#374151;margin-bottom:6px;">Gym Name <span style="color:#ef4444;">*</span></label>
                        <input type="text" name="name" value="{{ old('name', $gym->name) }}"
                               style="width:100%;padding:9px 12px;border:1px solid {{ $errors->has('name') ? '#ef4444' : '#d1d5db' }};border-radius:8px;font-size:14px;color:#0f172a;outline:none;box-sizing:border-box;">
                        @error('name')<p style="color:#ef4444;font-size:12px;margin:4px 0 0;">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label style="display:block;font-size:13px;font-weight:600;color:#374151;margin-bottom:6px;">Status <span style="color:#ef4444;">*</span></label>
                        <select name="status" style="width:100%;padding:9px 12px;border:1px solid #d1d5db;border-radius:8px;font-size:14px;color:#0f172a;outline:none;background:#fff;box-sizing:border-box;">
                            @foreach(['active','trial','suspended','inactive'] as $s)
                            <option value="{{ $s }}" {{ old('status', $gym->status) == $s ? 'selected' : '' }}>{{ ucfirst($s) }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;">
                        <div>
                            <label style="display:block;font-size:13px;font-weight:600;color:#374151;margin-bottom:6px;">Phone</label>
                            <input type="text" name="phone" value="{{ old('phone', $gym->phone) }}"
                                   style="width:100%;padding:9px 12px;border:1px solid #d1d5db;border-radius:8px;font-size:14px;color:#0f172a;outline:none;box-sizing:border-box;">
                        </div>
                        <div>
                            <label style="display:block;font-size:13px;font-weight:600;color:#374151;margin-bottom:6px;">Email</label>
                            <input type="email" name="email" value="{{ old('email', $gym->email) }}"
                                   style="width:100%;padding:9px 12px;border:1px solid #d1d5db;border-radius:8px;font-size:14px;color:#0f172a;outline:none;box-sizing:border-box;">
                        </div>
                    </div>

                    <div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:16px;">
                        <div>
                            <label style="display:block;font-size:13px;font-weight:600;color:#374151;margin-bottom:6px;">City</label>
                            <input type="text" name="city" value="{{ old('city', $gym->city) }}"
                                   style="width:100%;padding:9px 12px;border:1px solid #d1d5db;border-radius:8px;font-size:14px;color:#0f172a;outline:none;box-sizing:border-box;">
                        </div>
                        <div>
                            <label style="display:block;font-size:13px;font-weight:600;color:#374151;margin-bottom:6px;">Country</label>
                            <input type="text" name="country" value="{{ old('country', $gym->country) }}"
                                   style="width:100%;padding:9px 12px;border:1px solid #d1d5db;border-radius:8px;font-size:14px;color:#0f172a;outline:none;box-sizing:border-box;">
                        </div>
                        <div>
                            <label style="display:block;font-size:13px;font-weight:600;color:#374151;margin-bottom:6px;">Currency</label>
                            <select name="currency" style="width:100%;padding:9px 12px;border:1px solid #d1d5db;border-radius:8px;font-size:14px;color:#0f172a;outline:none;background:#fff;box-sizing:border-box;">
                                @foreach(['INR'=>'INR (₹)','USD'=>'USD ($)','EUR'=>'EUR (€)','GBP'=>'GBP (£)'] as $val => $label)
                                <option value="{{ $val }}" {{ old('currency', $gym->currency) == $val ? 'selected' : '' }}>{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div>
                        <label style="display:block;font-size:13px;font-weight:600;color:#374151;margin-bottom:6px;">Timezone</label>
                        <input type="text" name="timezone" value="{{ old('timezone', $gym->timezone) }}"
                               style="width:100%;padding:9px 12px;border:1px solid #d1d5db;border-radius:8px;font-size:14px;color:#0f172a;outline:none;box-sizing:border-box;">
                    </div>
                </div>
            </div>

            <div style="background:#fff;border-radius:14px;border:1px solid #e2e8f0;overflow:hidden;margin-bottom:16px;">
                <div style="padding:18px 24px;border-bottom:1px solid #f1f5f9;">
                    <h3 style="font-size:15px;font-weight:700;color:#0f172a;margin:0;">Owner Password</h3>
                </div>
                <div style="padding:24px;display:flex;flex-direction:column;gap:16px;">
                    <div style="padding:12px 14px;background:#f8fafc;border:1px solid #e2e8f0;border-radius:10px;">
                        <div style="font-size:12px;font-weight:700;color:#64748b;text-transform:uppercase;letter-spacing:.05em;margin-bottom:4px;">Owner Account</div>
                        <div style="font-size:14px;font-weight:600;color:#0f172a;">{{ $gym->owner?->name ?? 'No owner assigned' }}</div>
                        <div style="font-size:13px;color:#64748b;">{{ $gym->owner?->email ?? 'No owner email available' }}</div>
                    </div>

                    <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;">
                        <div>
                            <label style="display:block;font-size:13px;font-weight:600;color:#374151;margin-bottom:6px;">New Password</label>
                            <input type="password" name="owner_password"
                                   style="width:100%;padding:9px 12px;border:1px solid {{ $errors->has('owner_password') ? '#ef4444' : '#d1d5db' }};border-radius:8px;font-size:14px;color:#0f172a;outline:none;box-sizing:border-box;">
                            <p style="font-size:12px;color:#94a3b8;margin:6px 0 0;">Leave blank if you do not want to change the password.</p>
                            @error('owner_password')<p style="color:#ef4444;font-size:12px;margin:4px 0 0;">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label style="display:block;font-size:13px;font-weight:600;color:#374151;margin-bottom:6px;">Confirm Password</label>
                            <input type="password" name="owner_password_confirmation"
                                   style="width:100%;padding:9px 12px;border:1px solid #d1d5db;border-radius:8px;font-size:14px;color:#0f172a;outline:none;box-sizing:border-box;">
                        </div>
                    </div>
                </div>
            </div>

            <div style="display:flex;gap:10px;">
                <button type="submit" style="padding:10px 24px;background:#0abf8e;color:#fff;border:none;border-radius:8px;font-size:14px;font-weight:600;cursor:pointer;">Save Changes</button>
                <a href="{{ route('super-admin.gyms.show', $gym) }}" style="padding:10px 24px;background:#f1f5f9;color:#374151;border-radius:8px;text-decoration:none;font-size:14px;font-weight:600;">Cancel</a>
            </div>
        </form>
    </div>

</x-layouts.super-admin>
