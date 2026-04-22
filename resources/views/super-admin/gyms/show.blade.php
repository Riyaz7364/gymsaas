<x-layouts.super-admin>
    <x-slot:title>{{ $gym->name }} — Super Admin | {{ config('app.name') }}</x-slot:title>
    <x-slot:header>{{ $gym->name }}</x-slot:header>
    <x-slot:topbarTitle>Gym Details</x-slot:topbarTitle>
    <x-slot:breadcrumb>Super Admin / <a href="{{ route('super-admin.gyms.index') }}" style="color:#0abf8e;text-decoration:none;">Gyms</a> / {{ $gym->name }}</x-slot:breadcrumb>

    @if(session('success'))
    <div style="background:#dcfce7;border:1px solid #bbf7d0;color:#166534;padding:12px 16px;border-radius:8px;margin-bottom:16px;display:flex;align-items:center;gap:8px;">
        <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        {{ session('success') }}
    </div>
    @endif

    {{-- Top Action Bar --}}
    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:20px;">
        <a href="{{ route('super-admin.gyms.index') }}" style="display:inline-flex;align-items:center;gap:6px;font-size:13px;color:#64748b;text-decoration:none;">
            <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18"/></svg>
            All Gyms
        </a>
        <div style="display:flex;gap:8px;">
            <a href="{{ route('super-admin.gyms.edit', $gym) }}" style="padding:8px 18px;background:#eff6ff;color:#1d4ed8;border-radius:8px;text-decoration:none;font-size:13px;font-weight:600;">Edit Gym</a>
            <form method="POST" action="{{ route('super-admin.gyms.destroy', $gym) }}" onsubmit="return confirm('Delete {{ $gym->name }}? This cannot be undone.');">
                @csrf @method('DELETE')
                <button type="submit" style="padding:8px 18px;background:#fee2e2;color:#991b1b;border:none;border-radius:8px;font-size:13px;font-weight:600;cursor:pointer;">Delete</button>
            </form>
        </div>
    </div>

    <div style="display:grid;grid-template-columns:1fr 340px;gap:16px;align-items:start;">

        {{-- Left: Details --}}
        <div style="display:flex;flex-direction:column;gap:16px;">

            {{-- Info Card --}}
            <div style="background:#fff;border-radius:14px;border:1px solid #e2e8f0;overflow:hidden;">
                <div style="padding:18px 22px;border-bottom:1px solid #f1f5f9;display:flex;align-items:center;justify-content:space-between;">
                    <h3 style="font-size:15px;font-weight:700;color:#0f172a;margin:0;">Gym Information</h3>
                    @php
                        $statusColors = ['active'=>['#dcfce7','#166534'],'trial'=>['#fef9c3','#854d0e'],'suspended'=>['#fee2e2','#991b1b'],'inactive'=>['#f1f5f9','#475569']];
                        $sc = $statusColors[$gym->status] ?? ['#f1f5f9','#475569'];
                    @endphp
                    <span style="padding:4px 12px;border-radius:20px;font-size:12px;font-weight:600;background:{{ $sc[0] }};color:{{ $sc[1] }};">{{ ucfirst($gym->status) }}</span>
                </div>
                <div style="padding:20px 22px;">
                    <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px 32px;">
                        @php
                            $rows = [
                                ['label' => 'Gym Name',    'value' => $gym->name],
                                ['label' => 'Slug',        'value' => $gym->slug],
                                ['label' => 'Phone',       'value' => $gym->phone ?? '—'],
                                ['label' => 'Email',       'value' => $gym->email ?? '—'],
                                ['label' => 'City',        'value' => $gym->city ?? '—'],
                                ['label' => 'Country',     'value' => $gym->country ?? '—'],
                                ['label' => 'Currency',    'value' => $gym->currency ?? '—'],
                                ['label' => 'Timezone',    'value' => $gym->timezone ?? '—'],
                                ['label' => 'Members',     'value' => number_format($memberCount)],
                                ['label' => 'Trainers',    'value' => number_format($trainerCount)],
                            ];
                        @endphp
                        @foreach($rows as $row)
                        <div>
                            <div style="font-size:11px;font-weight:600;color:#94a3b8;text-transform:uppercase;letter-spacing:.05em;margin-bottom:3px;">{{ $row['label'] }}</div>
                            <div style="font-size:14px;color:#0f172a;font-weight:500;">{{ $row['value'] }}</div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>

            {{-- Subscription History --}}
            <div style="background:#fff;border-radius:14px;border:1px solid #e2e8f0;overflow:hidden;">
                <div style="padding:18px 22px;border-bottom:1px solid #f1f5f9;">
                    <h3 style="font-size:15px;font-weight:700;color:#0f172a;margin:0;">Subscription History</h3>
                </div>
                @if($gym->subscriptions->isEmpty())
                <div style="padding:32px;text-align:center;">
                    <p style="font-size:14px;color:#94a3b8;margin:0;">No subscriptions yet.</p>
                </div>
                @else
                <table style="width:100%;border-collapse:collapse;font-size:13px;">
                    <thead>
                        <tr style="background:#f8fafc;">
                            <th style="padding:10px 16px;text-align:left;font-size:11px;font-weight:700;color:#94a3b8;text-transform:uppercase;">Plan</th>
                            <th style="padding:10px 16px;text-align:left;font-size:11px;font-weight:700;color:#94a3b8;text-transform:uppercase;">Amount</th>
                            <th style="padding:10px 16px;text-align:left;font-size:11px;font-weight:700;color:#94a3b8;text-transform:uppercase;">Cycle</th>
                            <th style="padding:10px 16px;text-align:left;font-size:11px;font-weight:700;color:#94a3b8;text-transform:uppercase;">Status</th>
                            <th style="padding:10px 16px;text-align:left;font-size:11px;font-weight:700;color:#94a3b8;text-transform:uppercase;">Expires</th>
                        </tr>
                    </thead>
                    <tbody>
                    @foreach($gym->subscriptions as $sub)
                    @php
                        $subColors = ['active'=>['#dcfce7','#166534'],'trial'=>['#fef9c3','#854d0e'],'expired'=>['#fee2e2','#991b1b'],'cancelled'=>['#f1f5f9','#475569'],'past_due'=>['#ffedd5','#9a3412']];
                        $subsc = $subColors[$sub->status] ?? ['#f1f5f9','#475569'];
                    @endphp
                    <tr style="border-top:1px solid #f1f5f9;">
                        <td style="padding:10px 16px;font-weight:600;color:#0f172a;">{{ $sub->plan?->display_name ?? '—' }}</td>
                        <td style="padding:10px 16px;font-weight:700;color:#0f172a;">₹{{ number_format($sub->amount) }}</td>
                        <td style="padding:10px 16px;color:#64748b;">{{ ucfirst($sub->billing_cycle) }}</td>
                        <td style="padding:10px 16px;">
                            <span style="padding:2px 8px;border-radius:12px;font-size:12px;font-weight:600;background:{{ $subsc[0] }};color:{{ $subsc[1] }};">{{ ucfirst($sub->status) }}</span>
                        </td>
                        <td style="padding:10px 16px;font-size:12px;color:#64748b;">{{ $sub->expires_at ? $sub->expires_at->format('d M Y') : '—' }}</td>
                    </tr>
                    @endforeach
                    </tbody>
                </table>
                @endif
            </div>

        </div>

        {{-- Right: Owner + Quick Info --}}
        <div style="display:flex;flex-direction:column;gap:16px;">

            {{-- Owner Card --}}
            <div style="background:#fff;border-radius:14px;border:1px solid #e2e8f0;overflow:hidden;">
                <div style="padding:18px 22px;border-bottom:1px solid #f1f5f9;">
                    <h3 style="font-size:15px;font-weight:700;color:#0f172a;margin:0;">Gym Owner</h3>
                </div>
                <div style="padding:18px 22px;">
                    @if($gym->owner)
                    <div style="display:flex;align-items:center;gap:12px;margin-bottom:14px;">
                        <div style="width:44px;height:44px;border-radius:50%;background:linear-gradient(135deg,#0abf8e,#3b82f6);display:flex;align-items:center;justify-content:center;font-size:16px;font-weight:700;color:#fff;flex-shrink:0;">
                            {{ strtoupper(substr($gym->owner->name, 0, 1)) }}
                        </div>
                        <div>
                            <div style="font-size:14px;font-weight:700;color:#0f172a;">{{ $gym->owner->name }}</div>
                            <div style="font-size:13px;color:#64748b;">{{ $gym->owner->email }}</div>
                        </div>
                    </div>
                    <div style="display:flex;flex-direction:column;gap:8px;">
                        <div style="display:flex;justify-content:space-between;">
                            <span style="font-size:12px;color:#94a3b8;">Status</span>
                            <span style="font-size:12px;font-weight:600;color:{{ $gym->owner->status === 'active' ? '#166534' : '#991b1b' }};">{{ ucfirst($gym->owner->status) }}</span>
                        </div>
                        <div style="display:flex;justify-content:space-between;">
                            <span style="font-size:12px;color:#94a3b8;">Joined</span>
                            <span style="font-size:12px;color:#374151;">{{ $gym->owner->created_at->format('d M Y') }}</span>
                        </div>
                    </div>
                    @else
                    <p style="font-size:14px;color:#94a3b8;text-align:center;padding:12px 0;margin:0;">No owner assigned</p>
                    @endif
                </div>
            </div>

            {{-- Plan Summary --}}
            @if($gym->activeSubscription)
            @php $activeSub = $gym->activeSubscription; @endphp
            <div style="background:#0f172a;border-radius:14px;padding:20px 22px;">
                <div style="font-size:13px;font-weight:600;color:rgba(255,255,255,.5);text-transform:uppercase;letter-spacing:.06em;margin-bottom:12px;">Active Subscription</div>
                <div style="font-size:20px;font-weight:800;color:#fff;margin-bottom:4px;">{{ $activeSub->plan?->display_name }}</div>
                <div style="font-size:26px;font-weight:800;color:#0abf8e;margin-bottom:8px;">₹{{ number_format($activeSub->amount) }}<span style="font-size:14px;color:rgba(255,255,255,.4);font-weight:400;">/{{ $activeSub->billing_cycle === 'monthly' ? 'mo' : 'yr' }}</span></div>
                <div style="font-size:12px;color:rgba(255,255,255,.5);">
                    Started {{ $activeSub->started_at->format('d M Y') }}<br>
                    Expires {{ $activeSub->expires_at ? $activeSub->expires_at->format('d M Y') : '—' }}
                </div>
                @if($activeSub->expires_at && $activeSub->expires_at->lt(now()->addDays(7)))
                <div style="margin-top:12px;padding:8px 12px;background:rgba(239,68,68,.2);border-radius:8px;font-size:12px;color:#fca5a5;">
                    ⚠️ Expires in {{ now()->diffInDays($activeSub->expires_at) }} day(s)
                </div>
                @endif
            </div>
            @endif

            {{-- Stats --}}
            <div style="background:#fff;border-radius:14px;border:1px solid #e2e8f0;padding:18px 22px;">
                <div style="font-size:14px;font-weight:700;color:#0f172a;margin-bottom:14px;">Quick Stats</div>
                <div style="display:flex;flex-direction:column;gap:10px;">
                    <div style="display:flex;justify-content:space-between;align-items:center;">
                        <span style="font-size:13px;color:#64748b;">Total Members</span>
                        <span style="font-size:15px;font-weight:700;color:#0f172a;">{{ number_format($memberCount) }}</span>
                    </div>
                    <div style="display:flex;justify-content:space-between;align-items:center;">
                        <span style="font-size:13px;color:#64748b;">Trainers</span>
                        <span style="font-size:15px;font-weight:700;color:#0f172a;">{{ number_format($trainerCount) }}</span>
                    </div>
                    <div style="display:flex;justify-content:space-between;align-items:center;">
                        <span style="font-size:13px;color:#64748b;">Subscriptions</span>
                        <span style="font-size:15px;font-weight:700;color:#0f172a;">{{ $gym->subscriptions->count() }}</span>
                    </div>
                    <div style="display:flex;justify-content:space-between;align-items:center;">
                        <span style="font-size:13px;color:#64748b;">Registered</span>
                        <span style="font-size:13px;font-weight:600;color:#64748b;">{{ $gym->created_at->format('d M Y') }}</span>
                    </div>
                </div>
            </div>

        </div>
    </div>

</x-layouts.super-admin>
