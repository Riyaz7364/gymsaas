<x-layouts.super-admin>
    <x-slot:title>All Gyms - Super Admin | {{ config('app.name') }}</x-slot:title>
    <x-slot:header>All Gyms</x-slot:header>
    <x-slot:topbarTitle>Gyms on Platform</x-slot:topbarTitle>
    <x-slot:breadcrumb>Super Admin / All Gyms</x-slot:breadcrumb>

    @if(session('success'))
        <div style="background:#dcfce7;border:1px solid #bbf7d0;color:#166534;padding:12px 16px;border-radius:8px;margin-bottom:16px;display:flex;align-items:center;gap:8px;">
            <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0z"/></svg>
            {{ session('success') }}
        </div>
    @endif

    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:20px;">
        <div>
            <h2 style="font-size:20px;font-weight:700;color:#0f172a;margin:0 0 4px;">All Gyms</h2>
            <p style="font-size:13px;color:#64748b;margin:0;">{{ $gyms->total() }} gym{{ $gyms->total() != 1 ? 's' : '' }} registered on the platform</p>
        </div>
        <a href="{{ route('super-admin.gyms.create') }}"
           style="display:inline-flex;align-items:center;gap:8px;padding:10px 20px;background:#0abf8e;color:#fff;border-radius:8px;text-decoration:none;font-size:14px;font-weight:600;">
            <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/></svg>
            Add Gym
        </a>
    </div>

    <div style="background:#fff;border-radius:14px;border:1px solid #e2e8f0;overflow:hidden;">
        @if($gyms->isEmpty())
            <div style="padding:60px 20px;text-align:center;">
                <h3 style="font-size:16px;font-weight:600;color:#0f172a;margin:0 0 8px;">No gyms yet</h3>
                <p style="font-size:14px;color:#64748b;margin:0 0 16px;">Add your first gym to get started.</p>
                <a href="{{ route('super-admin.gyms.create') }}" style="display:inline-flex;align-items:center;gap:6px;padding:10px 20px;background:#0abf8e;color:#fff;border-radius:8px;text-decoration:none;font-size:14px;font-weight:600;">Add First Gym</a>
            </div>
        @else
            <div style="overflow-x:auto;">
                <table style="width:100%;border-collapse:collapse;font-size:13px;">
                    <thead>
                        <tr style="background:#f8fafc;border-bottom:1px solid #e2e8f0;">
                            <th style="padding:11px 16px;text-align:left;font-size:11px;font-weight:700;color:#94a3b8;text-transform:uppercase;letter-spacing:.05em;">Gym</th>
                            <th style="padding:11px 16px;text-align:left;font-size:11px;font-weight:700;color:#94a3b8;text-transform:uppercase;letter-spacing:.05em;">Subscriber</th>
                            <th style="padding:11px 16px;text-align:left;font-size:11px;font-weight:700;color:#94a3b8;text-transform:uppercase;letter-spacing:.05em;">Plan</th>
                            <th style="padding:11px 16px;text-align:left;font-size:11px;font-weight:700;color:#94a3b8;text-transform:uppercase;letter-spacing:.05em;">Status</th>
                            <th style="padding:11px 16px;text-align:left;font-size:11px;font-weight:700;color:#94a3b8;text-transform:uppercase;letter-spacing:.05em;">Subscription</th>
                            <th style="padding:11px 16px;text-align:left;font-size:11px;font-weight:700;color:#94a3b8;text-transform:uppercase;letter-spacing:.05em;">Joined</th>
                            <th style="padding:11px 16px;text-align:left;font-size:11px;font-weight:700;color:#94a3b8;text-transform:uppercase;letter-spacing:.05em;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($gyms as $gym)
                            @php
                                $statusColors = ['active' => ['#dcfce7', '#166534'], 'trial' => ['#fef9c3', '#854d0e'], 'suspended' => ['#fee2e2', '#991b1b'], 'inactive' => ['#f1f5f9', '#475569']];
                                $sc = $statusColors[$gym->status] ?? ['#f1f5f9', '#475569'];
                                $sub = $gym->activeSubscription;
                                $planLabel = $sub?->plan?->display_name ?? ucfirst((string) $gym->subscription_plan);
                            @endphp
                            <tr style="border-top:1px solid #f1f5f9;">
                                <td style="padding:12px 16px;">
                                    <div style="font-weight:600;color:#0f172a;">{{ $gym->name }}</div>
                                    <div style="font-size:11px;color:#94a3b8;margin-top:2px;">{{ $gym->city ? $gym->city . ', ' : '' }}{{ $gym->country ?? '-' }}</div>
                                </td>
                                <td style="padding:12px 16px;">
                                    @if($gym->owner)
                                        <div style="font-weight:500;color:#374151;">{{ $gym->owner->name }}</div>
                                        <div style="font-size:11px;color:#94a3b8;">{{ $gym->owner->email }}</div>
                                    @else
                                        <span style="color:#94a3b8;">-</span>
                                    @endif
                                </td>
                                <td style="padding:12px 16px;">
                                    <span style="padding:3px 10px;border-radius:20px;font-size:12px;font-weight:600;background:#f1f5f9;color:#334155;">
                                        {{ $planLabel }}
                                    </span>
                                </td>
                                <td style="padding:12px 16px;">
                                    <span style="padding:3px 10px;border-radius:20px;font-size:12px;font-weight:600;background:{{ $sc[0] }};color:{{ $sc[1] }};">
                                        {{ ucfirst($gym->status) }}
                                    </span>
                                </td>
                                <td style="padding:12px 16px;">
                                    @if($sub)
                                        <div style="font-size:13px;font-weight:600;color:#0f172a;">Rs {{ number_format($sub->amount) }}/{{ $sub->billing_cycle === 'monthly' ? 'mo' : 'yr' }}</div>
                                        <div style="font-size:11px;color:{{ $sub->expires_at && $sub->expires_at->lt(now()->addDays(7)) ? '#ef4444' : '#94a3b8' }};">
                                            Expires {{ $sub->expires_at ? $sub->expires_at->format('d M Y') : '-' }}
                                        </div>
                                    @else
                                        <span style="color:#94a3b8;font-size:13px;">Managed by subscriber plan</span>
                                    @endif
                                </td>
                                <td style="padding:12px 16px;font-size:12px;color:#64748b;">{{ $gym->created_at->format('d M Y') }}</td>
                                <td style="padding:12px 16px;">
                                    <div style="display:flex;gap:6px;">
                                        <a href="{{ route('super-admin.gyms.show', $gym) }}"
                                           style="padding:5px 12px;background:#f1f5f9;color:#374151;border-radius:6px;text-decoration:none;font-size:12px;font-weight:500;">View</a>
                                        <a href="{{ route('super-admin.gyms.edit', $gym) }}"
                                           style="padding:5px 12px;background:#eff6ff;color:#1d4ed8;border-radius:6px;text-decoration:none;font-size:12px;font-weight:500;">Edit</a>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            @if($gyms->hasPages())
                <div style="padding:14px 16px;border-top:1px solid #f1f5f9;">
                    {{ $gyms->links() }}
                </div>
            @endif
        @endif
    </div>
</x-layouts.super-admin>
