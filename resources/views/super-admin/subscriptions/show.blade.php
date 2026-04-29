<x-layouts.super-admin>
    <x-slot:title>{{ $subscriber->name }} - Subscriber Profile | {{ config('app.name') }}</x-slot:title>
    <x-slot:header>{{ $subscriber->name }}</x-slot:header>
    <x-slot:topbarTitle>Subscriber Profile</x-slot:topbarTitle>
    <x-slot:breadcrumb>Super Admin / <a href="{{ route('super-admin.subscriptions.index') }}" style="color:#0abf8e;text-decoration:none;">Subscribers</a> / {{ $subscriber->name }}</x-slot:breadcrumb>

    @php
        $currentSubscription = $subscriptionGym->activeSubscription ?: $subscriptionGym->subscriptions->first();
        $plan = $currentSubscription?->plan;
        $subscriptionTone = match ($currentSubscription?->status) {
            'active' => ['#dcfce7', '#166534'],
            'trial' => ['#fef9c3', '#854d0e'],
            'past_due' => ['#ffedd5', '#9a3412'],
            'cancelled' => ['#fee2e2', '#991b1b'],
            default => ['#f1f5f9', '#475569'],
        };
        $gymStatusColors = [
            'active' => ['#dcfce7', '#166534'],
            'trial' => ['#fef9c3', '#854d0e'],
            'suspended' => ['#fee2e2', '#991b1b'],
            'inactive' => ['#f1f5f9', '#475569'],
        ];
    @endphp

    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:20px;gap:12px;flex-wrap:wrap;">
        <a href="{{ route('super-admin.subscriptions.index') }}" style="display:inline-flex;align-items:center;gap:6px;font-size:13px;color:#64748b;text-decoration:none;">
            <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18"/></svg>
            Back to subscribers
        </a>

        <div style="display:flex;gap:8px;flex-wrap:wrap;">
            <a href="{{ route('super-admin.gyms.create', ['subscriber_id' => $subscriber->id]) }}" style="padding:10px 16px;background:#0abf8e;color:#fff;border-radius:10px;text-decoration:none;font-size:13px;font-weight:700;">Add Gym</a>
            <a href="{{ route('super-admin.gyms.show', $subscriptionGym) }}" style="padding:10px 16px;background:#eff6ff;color:#1d4ed8;border-radius:10px;text-decoration:none;font-size:13px;font-weight:700;">View Subscription Gym</a>
        </div>
    </div>

    <div style="display:grid;grid-template-columns:minmax(0,1.35fr) 340px;gap:16px;align-items:start;">
        <div style="display:flex;flex-direction:column;gap:16px;">
            <div style="background:#fff;border:1px solid #e2e8f0;border-radius:16px;overflow:hidden;">
                <div style="padding:20px 22px;border-bottom:1px solid #f1f5f9;">
                    <h3 style="font-size:16px;font-weight:800;color:#0f172a;margin:0;">Subscriber Details</h3>
                </div>

                <div style="padding:22px;">
                    <div style="display:flex;align-items:center;gap:14px;margin-bottom:20px;">
                        <div style="width:56px;height:56px;border-radius:50%;background:linear-gradient(135deg,#0abf8e,#3b82f6);display:flex;align-items:center;justify-content:center;color:#fff;font-size:20px;font-weight:800;">
                            {{ strtoupper(substr($subscriber->name, 0, 1)) }}
                        </div>
                        <div>
                            <div style="font-size:20px;font-weight:800;color:#0f172a;">{{ $subscriber->name }}</div>
                            <div style="font-size:14px;color:#64748b;">{{ $subscriber->email }}</div>
                        </div>
                    </div>

                    <div style="display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:16px 28px;">
                        <div>
                            <div style="font-size:11px;font-weight:700;color:#94a3b8;text-transform:uppercase;letter-spacing:.06em;margin-bottom:4px;">Phone</div>
                            <div style="font-size:14px;color:#0f172a;font-weight:600;">{{ $subscriber->phone ?: 'Not provided' }}</div>
                        </div>
                        <div>
                            <div style="font-size:11px;font-weight:700;color:#94a3b8;text-transform:uppercase;letter-spacing:.06em;margin-bottom:4px;">Status</div>
                            <div style="font-size:14px;color:#0f172a;font-weight:600;">{{ ucfirst($subscriber->status ?: 'active') }}</div>
                        </div>
                        <div>
                            <div style="font-size:11px;font-weight:700;color:#94a3b8;text-transform:uppercase;letter-spacing:.06em;margin-bottom:4px;">Subscriber Since</div>
                            <div style="font-size:14px;color:#0f172a;font-weight:600;">{{ $subscriber->created_at->format('d M Y') }}</div>
                        </div>
                        <div>
                            <div style="font-size:11px;font-weight:700;color:#94a3b8;text-transform:uppercase;letter-spacing:.06em;margin-bottom:4px;">Primary Subscription Gym</div>
                            <div style="font-size:14px;color:#0f172a;font-weight:600;">{{ $subscriptionGym->name }}</div>
                        </div>
                    </div>
                </div>
            </div>

            <div style="background:#fff;border:1px solid #e2e8f0;border-radius:16px;overflow:hidden;">
                <div style="padding:20px 22px;border-bottom:1px solid #f1f5f9;display:flex;align-items:center;justify-content:space-between;gap:12px;">
                    <div>
                        <h3 style="font-size:16px;font-weight:800;color:#0f172a;margin:0;">Gyms Under Subscriber</h3>
                        <p style="font-size:13px;color:#64748b;margin:6px 0 0;">Open any gym profile directly from here.</p>
                    </div>
                    <span style="padding:5px 10px;border-radius:999px;background:#eff6ff;color:#1d4ed8;font-size:12px;font-weight:800;">{{ $totals['gym_count'] }} total</span>
                </div>

                <div style="padding:16px;display:flex;flex-direction:column;gap:12px;">
                    @foreach($ownedGyms as $gym)
                        @php
                            $gymTone = $gymStatusColors[$gym->status] ?? ['#f1f5f9', '#475569'];
                            $gymSub = $gym->activeSubscription ?: $gym->subscriptions->first();
                        @endphp
                        <div style="border:1px solid #e2e8f0;border-radius:14px;padding:16px;background:#fff;">
                            <div style="display:flex;align-items:start;justify-content:space-between;gap:12px;flex-wrap:wrap;">
                                <div>
                                    <div style="font-size:15px;font-weight:800;color:#0f172a;">{{ $gym->name }}</div>
                                    <div style="font-size:13px;color:#64748b;margin-top:4px;">{{ $gym->city ?: 'Unknown city' }}{{ $gym->country ? ', ' . $gym->country : '' }}</div>
                                    <div style="font-size:13px;color:#94a3b8;margin-top:4px;">{{ $gymSub?->plan?->display_name ?? ucfirst((string) $gym->subscription_plan) }}</div>
                                </div>

                                <div style="display:flex;align-items:center;gap:8px;flex-wrap:wrap;">
                                    <span style="padding:4px 9px;border-radius:999px;background:{{ $gymTone[0] }};color:{{ $gymTone[1] }};font-size:11px;font-weight:800;">{{ ucfirst($gym->status) }}</span>
                                    <a href="{{ route('super-admin.gyms.show', $gym) }}" style="padding:8px 10px;background:#f1f5f9;color:#334155;border-radius:8px;text-decoration:none;font-size:12px;font-weight:700;">View Gym Profile</a>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <div style="display:flex;flex-direction:column;gap:16px;">
            <div style="background:#0f172a;border-radius:16px;padding:20px 22px;">
                <div style="font-size:12px;font-weight:700;color:rgba(255,255,255,.55);text-transform:uppercase;letter-spacing:.06em;margin-bottom:12px;">Subscriber Subscription</div>
                <div style="display:inline-flex;align-items:center;padding:5px 10px;border-radius:999px;background:{{ $subscriptionTone[0] }};color:{{ $subscriptionTone[1] }};font-size:12px;font-weight:800;margin-bottom:12px;">{{ ucfirst(str_replace('_', ' ', $currentSubscription?->status ?? 'inactive')) }}</div>
                <div style="font-size:22px;font-weight:800;color:#fff;margin-bottom:6px;">{{ $plan?->display_name ?? 'No plan' }}</div>
                <div style="font-size:28px;font-weight:900;color:#0abf8e;margin-bottom:10px;">{{ $currentSubscription ? 'Rs ' . number_format($currentSubscription->amount, 2) : 'NA' }}</div>
                <div style="font-size:13px;color:rgba(255,255,255,.7);line-height:1.8;">
                    <div>Billing cycle: {{ $currentSubscription ? ucfirst($currentSubscription->billing_cycle) : 'NA' }}</div>
                    <div>Started: {{ $currentSubscription?->started_at?->format('d M Y') ?: 'NA' }}</div>
                    <div>Expires: {{ $currentSubscription?->expires_at?->format('d M Y') ?: 'NA' }}</div>
                </div>
            </div>

            <div style="background:#fff;border:1px solid #e2e8f0;border-radius:16px;padding:20px 22px;">
                <div style="font-size:14px;font-weight:800;color:#0f172a;margin-bottom:14px;">Portfolio Summary</div>
                <div style="display:flex;flex-direction:column;gap:12px;">
                    <div style="display:flex;justify-content:space-between;align-items:center;">
                        <span style="font-size:13px;color:#64748b;">Total gyms</span>
                        <span style="font-size:15px;font-weight:800;color:#0f172a;">{{ number_format($totals['gym_count']) }}</span>
                    </div>
                    <div style="display:flex;justify-content:space-between;align-items:center;">
                        <span style="font-size:13px;color:#64748b;">Active gyms</span>
                        <span style="font-size:15px;font-weight:800;color:#0f172a;">{{ number_format($totals['active_gyms_count']) }}</span>
                    </div>
                    <div style="display:flex;justify-content:space-between;align-items:center;">
                        <span style="font-size:13px;color:#64748b;">Members across gyms</span>
                        <span style="font-size:15px;font-weight:800;color:#0f172a;">{{ number_format($totals['members_count']) }}</span>
                    </div>
                    <div style="display:flex;justify-content:space-between;align-items:center;">
                        <span style="font-size:13px;color:#64748b;">Trainers across gyms</span>
                        <span style="font-size:15px;font-weight:800;color:#0f172a;">{{ number_format($totals['trainers_count']) }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-layouts.super-admin>
