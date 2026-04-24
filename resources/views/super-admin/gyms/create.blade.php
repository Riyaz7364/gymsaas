<x-layouts.super-admin>
    <x-slot:title>Add Gym - Super Admin | {{ config('app.name') }}</x-slot:title>
    <x-slot:header>Add Gym</x-slot:header>
    <x-slot:topbarTitle>Assign Gym To Subscriber</x-slot:topbarTitle>
    <x-slot:breadcrumb>Super Admin / <a href="{{ route('super-admin.gyms.index') }}" style="color:#0abf8e;text-decoration:none;">Gyms</a> / Add</x-slot:breadcrumb>

    <div style="display:grid;grid-template-columns:minmax(0,1fr) 360px;gap:18px;align-items:start;">
        <div>
            <form method="POST" action="{{ route('super-admin.gyms.store') }}">
                @csrf

                <div style="background:#fff;border-radius:16px;border:1px solid #e2e8f0;overflow:hidden;margin-bottom:16px;">
                    <div style="padding:18px 24px;border-bottom:1px solid #f1f5f9;">
                        <h3 style="font-size:16px;font-weight:800;color:#0f172a;margin:0 0 6px;">Subscriber Assignment</h3>
                        <p style="font-size:13px;color:#64748b;margin:0;">Only subscribers whose active plan includes the Multi Branch module are shown here.</p>
                    </div>

                    <div style="padding:24px;display:flex;flex-direction:column;gap:18px;">
                        <div>
                            <label for="subscriber_id" style="display:block;font-size:13px;font-weight:700;color:#374151;margin-bottom:8px;">Select Subscriber <span style="color:#ef4444;">*</span></label>
                            <select id="subscriber_id" name="subscriber_id" style="width:100%;padding:11px 12px;border:1px solid {{ $errors->has('subscriber_id') ? '#ef4444' : '#cbd5e1' }};border-radius:10px;font-size:14px;color:#0f172a;background:#fff;">
                                <option value="">Choose an eligible subscriber</option>
                                @foreach($eligibleSubscribers as $subscriber)
                                    <option value="{{ $subscriber->id }}" @selected(old('subscriber_id', request('subscriber_id')) == $subscriber->id)>
                                        {{ $subscriber->name }} - {{ $subscriber->email }} - {{ $subscriber->gym_count }} gym{{ $subscriber->gym_count !== 1 ? 's' : '' }} - {{ $subscriber->source_plan_name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('subscriber_id')<p style="color:#ef4444;font-size:12px;margin:6px 0 0;">{{ $message }}</p>@enderror
                        </div>

                        @if($eligibleSubscribers->isEmpty())
                            <div style="padding:14px 16px;border-radius:12px;background:#fff7ed;border:1px solid #fed7aa;color:#9a3412;font-size:13px;">
                                No eligible subscribers were found. A subscriber must already have an active plan with the Multi Branch module before you can add another gym.
                            </div>
                        @else
                            <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(220px,1fr));gap:12px;">
                                @foreach($eligibleSubscribers as $subscriber)
                                    <div style="padding:14px;border:1px solid #e2e8f0;border-radius:14px;background:#f8fafc;">
                                        <div style="font-size:14px;font-weight:800;color:#0f172a;">{{ $subscriber->name }}</div>
                                        <div style="font-size:12px;color:#64748b;margin-top:4px;">{{ $subscriber->email }}</div>
                                        <div style="display:flex;gap:8px;flex-wrap:wrap;margin-top:10px;">
                                            <span style="padding:4px 9px;border-radius:999px;background:#dcfce7;color:#166534;font-size:11px;font-weight:800;">{{ $subscriber->source_plan_name }}</span>
                                            <span style="padding:4px 9px;border-radius:999px;background:#eff6ff;color:#1d4ed8;font-size:11px;font-weight:800;">{{ $subscriber->gym_count }} gym{{ $subscriber->gym_count !== 1 ? 's' : '' }}</span>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>

                <div style="background:#fff;border-radius:16px;border:1px solid #e2e8f0;overflow:hidden;margin-bottom:18px;">
                    <div style="padding:18px 24px;border-bottom:1px solid #f1f5f9;">
                        <h3 style="font-size:16px;font-weight:800;color:#0f172a;margin:0;">Gym Information</h3>
                    </div>

                    <div style="padding:24px;display:flex;flex-direction:column;gap:16px;">
                        <div>
                            <label style="display:block;font-size:13px;font-weight:700;color:#374151;margin-bottom:6px;">Gym Name <span style="color:#ef4444;">*</span></label>
                            <input type="text" name="name" value="{{ old('name') }}" placeholder="Fitness Pro - Andheri"
                                style="width:100%;padding:11px 12px;border:1px solid {{ $errors->has('name') ? '#ef4444' : '#cbd5e1' }};border-radius:10px;font-size:14px;color:#0f172a;">
                            @error('name')<p style="color:#ef4444;font-size:12px;margin:6px 0 0;">{{ $message }}</p>@enderror
                        </div>

                        <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;">
                            <div>
                                <label style="display:block;font-size:13px;font-weight:700;color:#374151;margin-bottom:6px;">Phone</label>
                                <input type="text" name="phone" value="{{ old('phone') }}" placeholder="+91 98765 43210"
                                    style="width:100%;padding:11px 12px;border:1px solid #cbd5e1;border-radius:10px;font-size:14px;color:#0f172a;">
                            </div>
                            <div>
                                <label style="display:block;font-size:13px;font-weight:700;color:#374151;margin-bottom:6px;">Email</label>
                                <input type="email" name="email" value="{{ old('email') }}" placeholder="branch@example.com"
                                    style="width:100%;padding:11px 12px;border:1px solid #cbd5e1;border-radius:10px;font-size:14px;color:#0f172a;">
                            </div>
                        </div>

                        <div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:16px;">
                            <div>
                                <label style="display:block;font-size:13px;font-weight:700;color:#374151;margin-bottom:6px;">City</label>
                                <input type="text" name="city" value="{{ old('city') }}" placeholder="Mumbai"
                                    style="width:100%;padding:11px 12px;border:1px solid #cbd5e1;border-radius:10px;font-size:14px;color:#0f172a;">
                            </div>
                            <div>
                                <label style="display:block;font-size:13px;font-weight:700;color:#374151;margin-bottom:6px;">Country</label>
                                <input type="text" name="country" value="{{ old('country', 'India') }}"
                                    style="width:100%;padding:11px 12px;border:1px solid #cbd5e1;border-radius:10px;font-size:14px;color:#0f172a;">
                            </div>
                            <div>
                                <label style="display:block;font-size:13px;font-weight:700;color:#374151;margin-bottom:6px;">Currency</label>
                                <select name="currency" style="width:100%;padding:11px 12px;border:1px solid #cbd5e1;border-radius:10px;font-size:14px;color:#0f172a;background:#fff;">
                                    <option value="INR" @selected(old('currency', 'INR') === 'INR')>INR (Rs)</option>
                                    <option value="USD" @selected(old('currency') === 'USD')>USD ($)</option>
                                    <option value="EUR" @selected(old('currency') === 'EUR')>EUR (EUR)</option>
                                    <option value="GBP" @selected(old('currency') === 'GBP')>GBP (GBP)</option>
                                </select>
                            </div>
                        </div>

                        <div>
                            <label style="display:block;font-size:13px;font-weight:700;color:#374151;margin-bottom:6px;">Timezone</label>
                            <input type="text" name="timezone" value="{{ old('timezone', 'Asia/Kolkata') }}"
                                style="width:100%;padding:11px 12px;border:1px solid #cbd5e1;border-radius:10px;font-size:14px;color:#0f172a;">
                        </div>
                    </div>
                </div>

                <div style="display:flex;gap:10px;">
                    <button type="submit" style="padding:11px 24px;background:#0abf8e;color:#fff;border:none;border-radius:10px;font-size:14px;font-weight:700;cursor:pointer;" @disabled($eligibleSubscribers->isEmpty())>Create Gym</button>
                    <a href="{{ route('super-admin.gyms.index') }}" style="padding:11px 24px;background:#f1f5f9;color:#374151;border-radius:10px;text-decoration:none;font-size:14px;font-weight:700;">Cancel</a>
                </div>
            </form>
        </div>

        <div style="display:flex;flex-direction:column;gap:16px;">
            <div style="background:#fff;border:1px solid #e2e8f0;border-radius:16px;overflow:hidden;">
                <div style="padding:18px 20px;border-bottom:1px solid #f1f5f9;">
                    <div style="font-size:15px;font-weight:800;color:#0f172a;">Existing Gyms</div>
                    <div style="font-size:13px;color:#64748b;margin-top:4px;">Quick reference so you can avoid duplicate branches.</div>
                </div>

                @if($gyms->isEmpty())
                    <div style="padding:18px 20px;font-size:13px;color:#64748b;">No gyms available yet.</div>
                @else
                    <div style="padding:10px 12px;display:flex;flex-direction:column;gap:10px;">
                        @foreach($gyms as $gym)
                            <a href="{{ route('super-admin.gyms.show', $gym) }}" style="display:block;padding:12px;border:1px solid #e2e8f0;border-radius:12px;text-decoration:none;background:#fff;">
                                <div style="font-size:14px;font-weight:800;color:#0f172a;">{{ $gym->name }}</div>
                                <div style="font-size:12px;color:#64748b;margin-top:4px;">Subscriber: {{ $gym->owner?->name ?? 'Unassigned' }}</div>
                                <div style="font-size:12px;color:#94a3b8;margin-top:4px;">{{ $gym->city ?: 'Unknown city' }}{{ $gym->country ? ', ' . $gym->country : '' }}</div>
                            </a>
                        @endforeach
                    </div>

                    @if($gyms->hasPages())
                        <div style="padding:12px 16px;border-top:1px solid #f1f5f9;">
                            {{ $gyms->links() }}
                        </div>
                    @endif
                @endif
            </div>
        </div>
    </div>
</x-layouts.super-admin>
