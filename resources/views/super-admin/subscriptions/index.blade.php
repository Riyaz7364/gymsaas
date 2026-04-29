<x-layouts.super-admin>
    <x-slot:title>Manage Subscribers - Super Admin | {{ config('app.name') }}</x-slot:title>
    <x-slot:header>Manage Subscribers</x-slot:header>
    <x-slot:topbarTitle>Subscriber List</x-slot:topbarTitle>
    <x-slot:breadcrumb>Super Admin / Manage Subscribers</x-slot:breadcrumb>

    @php
        $summaryCards = [
            ['label' => 'Total Subscribers', 'value' => number_format($overview['total_subscribers']), 'tone' => ['#eff6ff', '#1d4ed8']],
            ['label' => 'Active Subscribers', 'value' => number_format($overview['active_subscribers']), 'tone' => ['#dcfce7', '#166534']],
            ['label' => 'Multi Gym Subscribers', 'value' => number_format($overview['multi_gym_subscribers']), 'tone' => ['#f3e8ff', '#7c3aed']],
            ['label' => 'Gyms Under Subscribers', 'value' => number_format($overview['total_gyms']), 'tone' => ['#ecfeff', '#0f766e']],
            ['label' => 'Active Gyms', 'value' => number_format($overview['active_gyms']), 'tone' => ['#fef9c3', '#854d0e']],
            ['label' => 'Monthly Active Revenue', 'value' => 'Rs ' . number_format($overview['monthly_revenue'], 2), 'tone' => ['#fff7ed', '#c2410c']],
        ];

        $subStatusColors = [
            'active' => ['#dcfce7', '#166534'],
            'trial' => ['#fef9c3', '#854d0e'],
            'past_due' => ['#ffedd5', '#9a3412'],
            'cancelled' => ['#fee2e2', '#991b1b'],
            'expired' => ['#f1f5f9', '#475569'],
            'inactive' => ['#f1f5f9', '#475569'],
        ];

        $gymStatusColors = [
            'active' => ['#dcfce7', '#166534'],
            'trial' => ['#fef9c3', '#854d0e'],
            'suspended' => ['#fee2e2', '#991b1b'],
            'inactive' => ['#f1f5f9', '#475569'],
        ];
    @endphp

    <div style="display:grid;grid-template-columns:repeat(6,minmax(0,1fr));gap:14px;margin-bottom:20px;">
        @foreach($summaryCards as $card)
            <div style="background:#fff;border:1px solid #e2e8f0;border-radius:14px;padding:18px 16px;">
                <div style="font-size:11px;font-weight:700;color:#94a3b8;text-transform:uppercase;letter-spacing:.06em;margin-bottom:10px;">{{ $card['label'] }}</div>
                <div style="display:inline-flex;align-items:center;padding:5px 10px;border-radius:999px;background:{{ $card['tone'][0] }};color:{{ $card['tone'][1] }};font-size:12px;font-weight:700;margin-bottom:10px;">Live</div>
                <div style="font-size:24px;font-weight:800;color:#0f172a;line-height:1.1;">{{ $card['value'] }}</div>
            </div>
        @endforeach
    </div>

    <div style="background:#fff;border:1px solid #e2e8f0;border-radius:16px;padding:18px 20px;margin-bottom:18px;">
        <form method="GET" action="{{ route('super-admin.subscriptions.index') }}" style="display:grid;grid-template-columns:2fr 1fr 1fr 1fr 1fr auto auto;gap:12px;align-items:end;">
            <div>
                <label for="search" style="display:block;font-size:12px;font-weight:700;color:#64748b;margin-bottom:6px;">Search Subscriber</label>
                <input id="search" type="text" name="search" value="{{ $filters['search'] }}" placeholder="Subscriber, email, phone, gym name"
                    style="width:100%;padding:11px 12px;border:1px solid #cbd5e1;border-radius:10px;font-size:14px;color:#0f172a;background:#fff;">
            </div>

            <div>
                <label for="subscription_status" style="display:block;font-size:12px;font-weight:700;color:#64748b;margin-bottom:6px;">Subscription</label>
                <select id="subscription_status" name="subscription_status" style="width:100%;padding:11px 12px;border:1px solid #cbd5e1;border-radius:10px;font-size:14px;color:#0f172a;background:#fff;">
                    <option value="">All</option>
                    <option value="active" @selected($filters['subscription_status'] === 'active')>Active</option>
                    <option value="trial" @selected($filters['subscription_status'] === 'trial')>Trial</option>
                    <option value="past_due" @selected($filters['subscription_status'] === 'past_due')>Past Due</option>
                    <option value="inactive" @selected($filters['subscription_status'] === 'inactive')>Inactive</option>
                </select>
            </div>

            <div>
                <label for="gym_status" style="display:block;font-size:12px;font-weight:700;color:#64748b;margin-bottom:6px;">Gym Status</label>
                <select id="gym_status" name="gym_status" style="width:100%;padding:11px 12px;border:1px solid #cbd5e1;border-radius:10px;font-size:14px;color:#0f172a;background:#fff;">
                    <option value="">All</option>
                    <option value="active" @selected($filters['gym_status'] === 'active')>Active</option>
                    <option value="trial" @selected($filters['gym_status'] === 'trial')>Trial</option>
                    <option value="suspended" @selected($filters['gym_status'] === 'suspended')>Suspended</option>
                    <option value="inactive" @selected($filters['gym_status'] === 'inactive')>Inactive</option>
                </select>
            </div>

            <div>
                <label for="billing_cycle" style="display:block;font-size:12px;font-weight:700;color:#64748b;margin-bottom:6px;">Billing</label>
                <select id="billing_cycle" name="billing_cycle" style="width:100%;padding:11px 12px;border:1px solid #cbd5e1;border-radius:10px;font-size:14px;color:#0f172a;background:#fff;">
                    <option value="">All</option>
                    <option value="monthly" @selected($filters['billing_cycle'] === 'monthly')>Monthly</option>
                    <option value="annual" @selected($filters['billing_cycle'] === 'annual')>Annual</option>
                </select>
            </div>

            <div>
                <label for="plan_id" style="display:block;font-size:12px;font-weight:700;color:#64748b;margin-bottom:6px;">Plan</label>
                <select id="plan_id" name="plan_id" style="width:100%;padding:11px 12px;border:1px solid #cbd5e1;border-radius:10px;font-size:14px;color:#0f172a;background:#fff;">
                    <option value="">All Plans</option>
                    @foreach($plans as $planOption)
                        <option value="{{ $planOption->id }}" @selected($filters['plan_id'] === (string) $planOption->id)>{{ $planOption->display_name }}</option>
                    @endforeach
                </select>
            </div>

            <button type="submit" style="padding:11px 16px;background:#0abf8e;color:#fff;border:none;border-radius:10px;font-size:14px;font-weight:700;cursor:pointer;">Apply</button>
            <a href="{{ route('super-admin.subscriptions.index') }}" style="padding:11px 16px;background:#f8fafc;color:#334155;border:1px solid #e2e8f0;border-radius:10px;text-decoration:none;font-size:14px;font-weight:700;text-align:center;">Reset</a>
        </form>
    </div>

    <div style="background:#fff;border:1px solid #e2e8f0;border-radius:16px;overflow:hidden;">
        <div style="padding:16px 20px;border-bottom:1px solid #f1f5f9;display:flex;align-items:center;justify-content:space-between;gap:12px;flex-wrap:wrap;">
            <div>
                <div style="font-size:16px;font-weight:800;color:#0f172a;">Subscribers</div>
                <div style="font-size:13px;color:#64748b;margin-top:4px;">Showing {{ $subscribers->total() }} subscriber{{ $subscribers->total() !== 1 ? 's' : '' }} with subscription records.</div>
            </div>
        </div>

        @if($subscribers->isEmpty())
            <div style="padding:48px 24px;text-align:center;">
                <div style="font-size:17px;font-weight:700;color:#0f172a;margin-bottom:8px;">No subscribers matched these filters</div>
                <p style="font-size:14px;color:#64748b;margin:0;">Try changing the filters or reset them to view all subscriber profiles.</p>
            </div>
        @else
            <div style="display:flex;flex-direction:column;">
                @foreach($subscribers as $row)
                    @php
                        $subscriber = $row['subscriber'];
                        $subscriptionGym = $row['subscription_gym'];
                        $currentSubscription = $row['current_subscription'];
                        $plan = $row['plan'];
                        $subTone = $subStatusColors[$row['subscription_status']] ?? ['#f1f5f9', '#475569'];
                    @endphp

                    <div style="border-top:1px solid #f1f5f9;padding:18px 20px;">
                        <div style="display:grid;grid-template-columns:minmax(0,1.3fr) minmax(0,1fr) auto;gap:18px;align-items:start;">
                            <div>
                                <div style="display:flex;align-items:center;gap:12px;margin-bottom:10px;">
                                    <div style="width:44px;height:44px;border-radius:50%;background:linear-gradient(135deg,#0abf8e,#3b82f6);display:flex;align-items:center;justify-content:center;font-size:16px;font-weight:800;color:#fff;">
                                        {{ strtoupper(substr($subscriber->name, 0, 1)) }}
                                    </div>
                                    <div>
                                        <div style="font-size:16px;font-weight:800;color:#0f172a;">{{ $subscriber->name }}</div>
                                        <div style="font-size:13px;color:#64748b;">{{ $subscriber->email }}</div>
                                    </div>
                                </div>

                                <div style="display:flex;gap:8px;flex-wrap:wrap;margin-bottom:10px;">
                                    <span style="padding:4px 9px;border-radius:999px;background:{{ $subTone[0] }};color:{{ $subTone[1] }};font-size:11px;font-weight:800;">{{ ucfirst(str_replace('_', ' ', $row['subscription_status'])) }}</span>
                                    @if($row['has_multi_branch'])
                                        <span style="padding:4px 9px;border-radius:999px;background:#f3e8ff;color:#7c3aed;font-size:11px;font-weight:800;">Multi Gym Enabled</span>
                                    @endif
                                    <span style="padding:4px 9px;border-radius:999px;background:#eff6ff;color:#1d4ed8;font-size:11px;font-weight:800;">{{ $row['gym_count'] }} gym{{ $row['gym_count'] !== 1 ? 's' : '' }}</span>
                                </div>

                                <div style="font-size:13px;color:#64748b;line-height:1.8;">
                                    <div>Phone: <strong style="color:#0f172a;">{{ $subscriber->phone ?: 'Not provided' }}</strong></div>
                                    <div>Primary subscription gym: <strong style="color:#0f172a;">{{ $subscriptionGym->name }}</strong></div>
                                    <div>Joined: <strong style="color:#0f172a;">{{ $subscriber->created_at->format('d M Y') }}</strong></div>
                                </div>
                            </div>

                            <div>
                                <div style="font-size:14px;font-weight:800;color:#0f172a;">{{ $plan?->display_name ?? 'No plan' }}</div>
                                <div style="font-size:24px;font-weight:900;color:#0abf8e;line-height:1.1;margin:8px 0 10px;">{{ $currentSubscription ? 'Rs ' . number_format($currentSubscription->amount, 2) : 'NA' }}</div>
                                <div style="font-size:13px;color:#64748b;line-height:1.8;">
                                    <div>Billing: <strong style="color:#0f172a;">{{ $currentSubscription ? ucfirst($currentSubscription->billing_cycle) : 'NA' }}</strong></div>
                                    <div>Started: <strong style="color:#0f172a;">{{ $currentSubscription?->started_at?->format('d M Y') ?: 'NA' }}</strong></div>
                                    <div>Expires: <strong style="color:#0f172a;">{{ $currentSubscription?->expires_at?->format('d M Y') ?: 'NA' }}</strong></div>
                                    <div>Members across gyms: <strong style="color:#0f172a;">{{ number_format($row['members_count']) }}</strong></div>
                                    <div>Trainers across gyms: <strong style="color:#0f172a;">{{ number_format($row['trainers_count']) }}</strong></div>
                                </div>
                            </div>

                            <div style="display:flex;flex-direction:column;gap:8px;min-width:150px;">
                                <a href="{{ route('super-admin.subscriptions.show', $subscriber) }}" style="padding:10px 12px;background:#eff6ff;color:#1d4ed8;border-radius:10px;text-decoration:none;font-size:13px;font-weight:700;text-align:center;">View Profile</a>
                                <a href="{{ route('super-admin.gyms.create', ['subscriber_id' => $subscriber->id]) }}" style="padding:10px 12px;background:#f8fafc;color:#334155;border:1px solid #e2e8f0;border-radius:10px;text-decoration:none;font-size:13px;font-weight:700;text-align:center;">Add Gym</a>
                            </div>
                        </div>

                        <details style="margin-top:16px;border:1px solid #e2e8f0;border-radius:12px;background:#f8fafc;">
                            <summary style="padding:12px 14px;cursor:pointer;font-size:13px;font-weight:700;color:#334155;">Show gyms under this subscriber</summary>
                            <div style="padding:0 14px 14px;display:flex;flex-direction:column;gap:10px;">
                                @foreach($row['owned_gyms'] as $gym)
                                    @php
                                        $gymTone = $gymStatusColors[$gym->status] ?? ['#f1f5f9', '#475569'];
                                        $gymSub = $gym->activeSubscription ?: $gym->subscriptions->first();
                                    @endphp
                                    <div style="padding:12px;background:#fff;border:1px solid #e2e8f0;border-radius:12px;display:flex;align-items:center;justify-content:space-between;gap:12px;flex-wrap:wrap;">
                                        <div>
                                            <div style="font-size:14px;font-weight:800;color:#0f172a;">{{ $gym->name }}</div>
                                            <div style="font-size:12px;color:#64748b;margin-top:4px;">{{ $gym->city ?: 'Unknown city' }}{{ $gym->country ? ', ' . $gym->country : '' }}</div>
                                            <div style="font-size:12px;color:#94a3b8;margin-top:4px;">{{ $gymSub?->plan?->display_name ?? ucfirst((string) $gym->subscription_plan) }}</div>
                                        </div>
                                        <div style="display:flex;align-items:center;gap:8px;flex-wrap:wrap;">
                                            <span style="padding:4px 9px;border-radius:999px;background:{{ $gymTone[0] }};color:{{ $gymTone[1] }};font-size:11px;font-weight:800;">{{ ucfirst($gym->status) }}</span>
                                            <a href="{{ route('super-admin.gyms.show', $gym) }}" style="padding:8px 10px;background:#f1f5f9;color:#334155;border-radius:8px;text-decoration:none;font-size:12px;font-weight:700;">View Gym</a>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </details>
                    </div>
                @endforeach
            </div>

            <div style="padding:16px 20px;border-top:1px solid #f1f5f9;">
                {{ $subscribers->links() }}
            </div>
        @endif
    </div>
</x-layouts.super-admin>
