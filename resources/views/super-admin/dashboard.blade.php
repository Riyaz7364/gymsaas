<x-layouts.super-admin>
    <x-slot:title>Dashboard — Super Admin | {{ config('app.name') }}</x-slot:title>
    <x-slot:header>Dashboard</x-slot:header>
    <x-slot:topbarTitle>Platform Overview</x-slot:topbarTitle>
    <x-slot:breadcrumb>Super Admin / Dashboard</x-slot:breadcrumb>

    {{-- ── Top KPI Cards ─────────────────────────────────────────────── --}}
    <div style="display:grid;grid-template-columns:repeat(4,1fr);gap:16px;margin-bottom:20px;">

        {{-- MRR --}}
        <div style="background:#fff;border-radius:12px;border:1px solid #e2e8f0;padding:20px 22px;position:relative;overflow:hidden;">
            <div style="position:absolute;top:0;right:0;width:80px;height:80px;background:linear-gradient(135deg,rgba(10,191,142,.12),rgba(10,191,142,.04));border-radius:0 12px 0 80px;"></div>
            <div style="font-size:12px;font-weight:600;color:#64748b;text-transform:uppercase;letter-spacing:.06em;margin-bottom:6px;">Monthly Recurring Revenue</div>
            <div style="font-size:28px;font-weight:800;color:#0f172a;line-height:1.1;">₹{{ number_format($mrr) }}</div>
            <div style="display:flex;align-items:center;gap:6px;margin-top:8px;">
                <span style="font-size:12px;font-weight:600;background:{{ $revenueGrowth >= 0 ? '#dcfce7' : '#fee2e2' }};color:{{ $revenueGrowth >= 0 ? '#166534' : '#991b1b' }};padding:2px 8px;border-radius:20px;">{{ $revenueGrowth >= 0 ? '+' : '' }}{{ $revenueGrowth }}%</span>
                <span style="font-size:12px;color:#94a3b8;">vs last month</span>
            </div>
        </div>

        {{-- ARR --}}
        <div style="background:#fff;border-radius:12px;border:1px solid #e2e8f0;padding:20px 22px;position:relative;overflow:hidden;">
            <div style="position:absolute;top:0;right:0;width:80px;height:80px;background:linear-gradient(135deg,rgba(59,130,246,.12),rgba(59,130,246,.04));border-radius:0 12px 0 80px;"></div>
            <div style="font-size:12px;font-weight:600;color:#64748b;text-transform:uppercase;letter-spacing:.06em;margin-bottom:6px;">Annual Recurring Revenue</div>
            <div style="font-size:28px;font-weight:800;color:#0f172a;line-height:1.1;">₹{{ number_format($arr) }}</div>
            <div style="margin-top:8px;">
                <span style="font-size:12px;color:#94a3b8;">Projected annual from current MRR</span>
            </div>
        </div>

        {{-- Active Subs --}}
        <div style="background:#fff;border-radius:12px;border:1px solid #e2e8f0;padding:20px 22px;position:relative;overflow:hidden;">
            <div style="position:absolute;top:0;right:0;width:80px;height:80px;background:linear-gradient(135deg,rgba(168,85,247,.12),rgba(168,85,247,.04));border-radius:0 12px 0 80px;"></div>
            <div style="font-size:12px;font-weight:600;color:#64748b;text-transform:uppercase;letter-spacing:.06em;margin-bottom:6px;">Active Subscriptions</div>
            <div style="font-size:28px;font-weight:800;color:#0f172a;line-height:1.1;">{{ $activeSubscriptions }}</div>
            <div style="display:flex;align-items:center;gap:6px;margin-top:8px;">
                <span style="font-size:12px;font-weight:600;background:#fef9c3;color:#854d0e;padding:2px 8px;border-radius:20px;">{{ $trialSubscriptions }} trial</span>
                <span style="font-size:12px;color:#94a3b8;">{{ $totalGyms }} gyms total</span>
            </div>
        </div>

        {{-- Expiring / At Risk --}}
        <div style="background:#fff;border-radius:12px;border:1px solid #e2e8f0;padding:20px 22px;position:relative;overflow:hidden;">
            <div style="position:absolute;top:0;right:0;width:80px;height:80px;background:linear-gradient(135deg,rgba(239,68,68,.12),rgba(239,68,68,.04));border-radius:0 12px 0 80px;"></div>
            <div style="font-size:12px;font-weight:600;color:#64748b;text-transform:uppercase;letter-spacing:.06em;margin-bottom:6px;">Expiring Soon</div>
            <div style="font-size:28px;font-weight:800;color:{{ $expiringIn7Days > 0 ? '#ef4444' : '#0f172a' }};line-height:1.1;">{{ $expiringIn7Days }}</div>
            <div style="margin-top:8px;">
                <span style="font-size:12px;color:#94a3b8;">Renewing within 7 days · {{ $expiredSubscriptions }} expired</span>
            </div>
        </div>

    </div>

    {{-- ── Charts Row ────────────────────────────────────────────────── --}}
    <div style="display:grid;grid-template-columns:1fr 360px;gap:16px;margin-bottom:20px;">

        {{-- Revenue Trend Chart --}}
        <div style="background:#fff;border-radius:12px;border:1px solid #e2e8f0;padding:20px 22px;">
            <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:20px;">
                <div>
                    <div style="font-size:15px;font-weight:700;color:#0f172a;">Revenue Trend</div>
                    <div style="font-size:12px;color:#94a3b8;margin-top:2px;">Monthly recurring revenue — last 6 months</div>
                </div>
                <div style="display:flex;gap:8px;">
                    <span style="font-size:12px;font-weight:600;background:#ecfdf5;color:#065f46;padding:4px 12px;border-radius:20px;">MRR</span>
                </div>
            </div>
            <canvas id="revenueChart" height="100"></canvas>
        </div>

        {{-- Subscription Plan Distribution Donut --}}
        <div style="background:#fff;border-radius:12px;border:1px solid #e2e8f0;padding:20px 22px;">
            <div style="font-size:15px;font-weight:700;color:#0f172a;margin-bottom:4px;">Subscriptions by Plan</div>
            <div style="font-size:12px;color:#94a3b8;margin-bottom:20px;">Active paying customers</div>
            <div style="display:flex;justify-content:center;margin-bottom:16px;">
                <canvas id="planChart" width="180" height="180"></canvas>
            </div>
            <div style="display:flex;flex-direction:column;gap:8px;">
                @php
                    $planColors = ['basic' => '#3b82f6', 'pro' => '#0abf8e', 'enterprise' => '#a855f7'];
                @endphp
                @foreach($planStats as $plan)
                <div style="display:flex;align-items:center;justify-content:space-between;">
                    <div style="display:flex;align-items:center;gap:8px;">
                        <div style="width:10px;height:10px;border-radius:50%;background:{{ $planColors[$plan->name] ?? '#94a3b8' }};"></div>
                        <span style="font-size:13px;color:#4b5563;">{{ $plan->display_name }}</span>
                    </div>
                    <span style="font-size:13px;font-weight:700;color:#0f172a;">{{ $plan->active_count }}</span>
                </div>
                @endforeach
                @if($planStats->isEmpty())
                <p style="text-align:center;font-size:13px;color:#94a3b8;padding:12px 0;">No active subscriptions yet</p>
                @endif
            </div>
        </div>
    </div>

    {{-- ── Bottom Row: Gym Growth + Quick Actions + Recent Subs ───────── --}}
    <div style="display:grid;grid-template-columns:1fr 340px;gap:16px;">

        {{-- Left: Gym Signups + Recent Subscriptions --}}
        <div style="display:flex;flex-direction:column;gap:16px;">

            {{-- Gym Signup Chart --}}
            <div style="background:#fff;border-radius:12px;border:1px solid #e2e8f0;padding:20px 22px;">
                <div style="font-size:15px;font-weight:700;color:#0f172a;margin-bottom:4px;">New Gym Signups</div>
                <div style="font-size:12px;color:#94a3b8;margin-bottom:16px;">New gyms joining the platform monthly</div>
                <canvas id="gymGrowthChart" height="60"></canvas>
            </div>

            {{-- Recent Subscriptions Table --}}
            <div style="background:#fff;border-radius:12px;border:1px solid #e2e8f0;overflow:hidden;">
                <div style="padding:16px 20px;border-bottom:1px solid #f1f5f9;display:flex;align-items:center;justify-content:space-between;">
                    <div style="font-size:15px;font-weight:700;color:#0f172a;">Recent Subscriptions</div>
                    <a href="{{ route('super-admin.gyms.index') }}" style="font-size:13px;color:#0abf8e;text-decoration:none;font-weight:500;">View all gyms →</a>
                </div>
                @if($recentSubscriptions->isEmpty())
                <div style="padding:48px 20px;text-align:center;">
                    <div style="font-size:36px;margin-bottom:12px;">📋</div>
                    <p style="color:#94a3b8;font-size:14px;">No subscriptions yet</p>
                    <a href="{{ route('super-admin.gyms.create') }}" style="display:inline-flex;align-items:center;gap:6px;margin-top:12px;padding:8px 18px;background:#0abf8e;color:#fff;border-radius:8px;text-decoration:none;font-size:14px;font-weight:500;">Add First Gym</a>
                </div>
                @else
                <table style="width:100%;border-collapse:collapse;font-size:13px;">
                    <thead>
                        <tr style="background:#f8fafc;">
                            <th style="padding:10px 16px;text-align:left;font-size:11px;font-weight:700;color:#94a3b8;text-transform:uppercase;letter-spacing:.05em;">Gym</th>
                            <th style="padding:10px 16px;text-align:left;font-size:11px;font-weight:700;color:#94a3b8;text-transform:uppercase;letter-spacing:.05em;">Plan</th>
                            <th style="padding:10px 16px;text-align:left;font-size:11px;font-weight:700;color:#94a3b8;text-transform:uppercase;letter-spacing:.05em;">Amount</th>
                            <th style="padding:10px 16px;text-align:left;font-size:11px;font-weight:700;color:#94a3b8;text-transform:uppercase;letter-spacing:.05em;">Status</th>
                            <th style="padding:10px 16px;text-align:left;font-size:11px;font-weight:700;color:#94a3b8;text-transform:uppercase;letter-spacing:.05em;">Expires</th>
                        </tr>
                    </thead>
                    <tbody>
                    @foreach($recentSubscriptions as $sub)
                    @php
                        $statusColors = [
                            'active'    => ['#dcfce7','#166534'],
                            'trial'     => ['#fef9c3','#854d0e'],
                            'expired'   => ['#fee2e2','#991b1b'],
                            'cancelled' => ['#f1f5f9','#475569'],
                            'past_due'  => ['#ffedd5','#9a3412'],
                        ];
                        $sc = $statusColors[$sub->status] ?? ['#f1f5f9','#475569'];
                    @endphp
                    <tr style="border-top:1px solid #f1f5f9;">
                        <td style="padding:11px 16px;">
                            <div style="font-weight:600;color:#0f172a;">{{ $sub->gym?->name ?? '—' }}</div>
                            <div style="font-size:11px;color:#94a3b8;">{{ ucfirst($sub->billing_cycle) }}</div>
                        </td>
                        <td style="padding:11px 16px;">
                            <span style="font-weight:600;color:#4b5563;">{{ $sub->plan?->display_name ?? '—' }}</span>
                        </td>
                        <td style="padding:11px 16px;font-weight:700;color:#0f172a;">₹{{ number_format($sub->amount) }}</td>
                        <td style="padding:11px 16px;">
                            <span style="padding:2px 8px;border-radius:20px;font-size:12px;font-weight:600;background:{{ $sc[0] }};color:{{ $sc[1] }};">{{ ucfirst($sub->status) }}</span>
                        </td>
                        <td style="padding:11px 16px;font-size:12px;color:#64748b;">
                            {{ $sub->expires_at ? $sub->expires_at->format('d M Y') : '—' }}
                        </td>
                    </tr>
                    @endforeach
                    </tbody>
                </table>
                @endif
            </div>

        </div>

        {{-- Right: Quick Actions --}}
        <div style="display:flex;flex-direction:column;gap:12px;">

            <div style="background:#fff;border-radius:12px;border:1px solid #e2e8f0;padding:18px 20px;">
                <div style="font-size:15px;font-weight:700;color:#0f172a;margin-bottom:14px;">Quick Actions</div>
                <div style="display:flex;flex-direction:column;gap:8px;">

                    <a href="{{ route('super-admin.gyms.create') }}"
                       style="display:flex;align-items:center;gap:12px;padding:12px 14px;border-radius:10px;background:#ecfdf5;border:1px solid #bbf7d0;text-decoration:none;transition:transform .1s;"
                       onmouseover="this.style.transform='translateX(3px)'" onmouseout="this.style.transform='translateX(0)'">
                        <div style="width:36px;height:36px;border-radius:8px;background:#0abf8e;color:#fff;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                            <svg width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/></svg>
                        </div>
                        <div>
                            <div style="font-size:13px;font-weight:700;color:#065f46;">Add New Gym</div>
                            <div style="font-size:12px;color:#16a34a;">Onboard a paying customer</div>
                        </div>
                        <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="#16a34a" stroke-width="2" style="margin-left:auto;"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
                    </a>

                    <a href="{{ route('super-admin.pricing.index') }}"
                       style="display:flex;align-items:center;gap:12px;padding:12px 14px;border-radius:10px;background:#eff6ff;border:1px solid #bfdbfe;text-decoration:none;transition:transform .1s;"
                       onmouseover="this.style.transform='translateX(3px)'" onmouseout="this.style.transform='translateX(0)'">
                        <div style="width:36px;height:36px;border-radius:8px;background:#3b82f6;color:#fff;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                            <svg width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v12m-3-2.818.879.659c1.171.879 3.07.879 4.242 0 1.172-.879 1.172-2.303 0-3.182C13.536 12.219 12.768 12 12 12c-.725 0-1.45-.22-2.003-.659-1.106-.879-1.106-2.303 0-3.182s2.9-.879 4.006 0l.415.33M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        </div>
                        <div>
                            <div style="font-size:13px;font-weight:700;color:#1d4ed8;">Manage Pricing Plans</div>
                            <div style="font-size:12px;color:#3b82f6;">Edit Basic / Pro / Enterprise</div>
                        </div>
                        <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="#3b82f6" stroke-width="2" style="margin-left:auto;"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
                    </a>

                    <a href="{{ route('super-admin.gyms.index') }}"
                       style="display:flex;align-items:center;gap:12px;padding:12px 14px;border-radius:10px;background:#fdf4ff;border:1px solid #e9d5ff;text-decoration:none;transition:transform .1s;"
                       onmouseover="this.style.transform='translateX(3px)'" onmouseout="this.style.transform='translateX(0)'">
                        <div style="width:36px;height:36px;border-radius:8px;background:#a855f7;color:#fff;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                            <svg width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.2"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 21h16.5M4.5 3h15M5.25 3v18m13.5-18v18M9 6.75h1.5m-1.5 3h1.5m-1.5 3h1.5m3-6H15m-1.5 3H15m-1.5 3H15M9 21v-3.375c0-.621.504-1.125 1.125-1.125h3.75c.621 0 1.125.504 1.125 1.125V21"/></svg>
                        </div>
                        <div>
                            <div style="font-size:13px;font-weight:700;color:#7e22ce;">All Gyms</div>
                            <div style="font-size:12px;color:#a855f7;">{{ $totalGyms }} registered gyms</div>
                        </div>
                        <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="#a855f7" stroke-width="2" style="margin-left:auto;"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
                    </a>

                    <a href="{{ route('super-admin.settings') }}"
                       style="display:flex;align-items:center;gap:12px;padding:12px 14px;border-radius:10px;background:#fff7ed;border:1px solid #fed7aa;text-decoration:none;transition:transform .1s;"
                       onmouseover="this.style.transform='translateX(3px)'" onmouseout="this.style.transform='translateX(0)'">
                        <div style="width:36px;height:36px;border-radius:8px;background:#f97316;color:#fff;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                            <svg width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.2"><path stroke-linecap="round" stroke-linejoin="round" d="M9.594 3.94c.09-.542.56-.94 1.11-.94h2.593c.55 0 1.02.398 1.11.94l.213 1.281c.063.374.313.686.645.87.074.04.147.083.22.127.324.196.72.257 1.075.124l1.217-.456a1.125 1.125 0 011.37.49l1.296 2.247a1.125 1.125 0 01-.26 1.431l-1.003.827c-.293.24-.438.613-.431.992a6.759 6.759 0 010 .255c-.007.378.138.75.43.99l1.005.828c.424.35.534.954.26 1.43l-1.298 2.247a1.125 1.125 0 01-1.369.491l-1.217-.456c-.355-.133-.75-.072-1.076.124a6.57 6.57 0 01-.22.128c-.331.183-.581.495-.644.869l-.213 1.28c-.09.543-.56.941-1.11.941h-2.594c-.55 0-1.02-.398-1.11-.94l-.213-1.281c-.062-.374-.312-.686-.644-.87a6.52 6.52 0 01-.22-.127c-.325-.196-.72-.257-1.076-.124l-1.217.456a1.125 1.125 0 01-1.369-.49l-1.297-2.247a1.125 1.125 0 01.26-1.431l1.004-.827c.292-.24.437-.613.43-.992a6.932 6.932 0 010-.255c.007-.378-.138-.75-.43-.99l-1.004-.828a1.125 1.125 0 01-.26-1.43l1.297-2.247a1.125 1.125 0 011.37-.491l1.216.456c.356.133.751.072 1.076-.124.072-.044.146-.087.22-.128.332-.183.582-.495.644-.869l.214-1.281z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                        </div>
                        <div>
                            <div style="font-size:13px;font-weight:700;color:#c2410c;">Platform Settings</div>
                            <div style="font-size:12px;color:#f97316;">Configure global options</div>
                        </div>
                        <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="#f97316" stroke-width="2" style="margin-left:auto;"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
                    </a>
                </div>
            </div>

            {{-- Subscription Health Card --}}
            <div style="background:#0f172a;border-radius:12px;padding:18px 20px;">
                <div style="font-size:14px;font-weight:700;color:#fff;margin-bottom:14px;">Subscription Health</div>
                @php
                    $total = $activeSubscriptions + $trialSubscriptions + $expiredSubscriptions;
                    $activePercent = $total > 0 ? round(($activeSubscriptions / $total) * 100) : 0;
                    $trialPercent  = $total > 0 ? round(($trialSubscriptions / $total) * 100) : 0;
                    $expiredPercent = $total > 0 ? round(($expiredSubscriptions / $total) * 100) : 0;
                @endphp
                <div style="display:flex;flex-direction:column;gap:10px;">
                    <div>
                        <div style="display:flex;justify-content:space-between;margin-bottom:4px;">
                            <span style="font-size:12px;color:rgba(255,255,255,.6);">Active</span>
                            <span style="font-size:12px;font-weight:700;color:#0abf8e;">{{ $activeSubscriptions }} ({{ $activePercent }}%)</span>
                        </div>
                        <div style="background:rgba(255,255,255,.1);border-radius:4px;height:6px;"><div style="background:#0abf8e;width:{{ $activePercent }}%;height:6px;border-radius:4px;"></div></div>
                    </div>
                    <div>
                        <div style="display:flex;justify-content:space-between;margin-bottom:4px;">
                            <span style="font-size:12px;color:rgba(255,255,255,.6);">Trial</span>
                            <span style="font-size:12px;font-weight:700;color:#f59e0b;">{{ $trialSubscriptions }} ({{ $trialPercent }}%)</span>
                        </div>
                        <div style="background:rgba(255,255,255,.1);border-radius:4px;height:6px;"><div style="background:#f59e0b;width:{{ $trialPercent }}%;height:6px;border-radius:4px;"></div></div>
                    </div>
                    <div>
                        <div style="display:flex;justify-content:space-between;margin-bottom:4px;">
                            <span style="font-size:12px;color:rgba(255,255,255,.6);">Expired / Cancelled</span>
                            <span style="font-size:12px;font-weight:700;color:#ef4444;">{{ $expiredSubscriptions }} ({{ $expiredPercent }}%)</span>
                        </div>
                        <div style="background:rgba(255,255,255,.1);border-radius:4px;height:6px;"><div style="background:#ef4444;width:{{ $expiredPercent }}%;height:6px;border-radius:4px;"></div></div>
                    </div>
                </div>
                @if($expiringIn7Days > 0)
                <div style="margin-top:14px;padding:10px 12px;background:rgba(239,68,68,.15);border-radius:8px;border:1px solid rgba(239,68,68,.25);">
                    <div style="font-size:12px;color:#fca5a5;font-weight:600;">⚠️ {{ $expiringIn7Days }} subscription{{ $expiringIn7Days > 1 ? 's' : '' }} expiring in 7 days</div>
                </div>
                @endif
            </div>

        </div>
    </div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
// ── Revenue Trend Line Chart ────────────────────────────────────────────────
const revenueCtx = document.getElementById('revenueChart').getContext('2d');
new Chart(revenueCtx, {
    type: 'line',
    data: {
        labels: {!! json_encode($monthlyLabels) !!},
        datasets: [{
            label: 'MRR (₹)',
            data: {!! json_encode($monthlyRevenue) !!},
            borderColor: '#0abf8e',
            backgroundColor: 'rgba(10,191,142,0.08)',
            borderWidth: 2.5,
            pointBackgroundColor: '#0abf8e',
            pointRadius: 4,
            pointHoverRadius: 6,
            fill: true,
            tension: 0.4,
        }]
    },
    options: {
        responsive: true,
        plugins: {
            legend: { display: false },
            tooltip: {
                callbacks: {
                    label: ctx => '₹' + ctx.parsed.y.toLocaleString('en-IN')
                }
            }
        },
        scales: {
            x: { grid: { display: false }, ticks: { color: '#94a3b8', font: { size: 12 } } },
            y: {
                beginAtZero: true,
                grid: { color: 'rgba(0,0,0,.05)' },
                ticks: {
                    color: '#94a3b8',
                    font: { size: 11 },
                    callback: val => '₹' + (val >= 1000 ? (val/1000).toFixed(0)+'K' : val)
                }
            }
        }
    }
});

// ── Plan Distribution Donut Chart ───────────────────────────────────────────
const planCtx = document.getElementById('planChart').getContext('2d');
const planData = {{ json_encode($planStats->pluck('active_count')->values()) }};
const planLabels = {{ json_encode($planStats->pluck('display_name')->values()) }};
const planColors = ['#3b82f6', '#0abf8e', '#a855f7'];
const hasData = planData.some(v => v > 0);
new Chart(planCtx, {
    type: 'doughnut',
    data: {
        labels: hasData ? planLabels : ['No subscriptions'],
        datasets: [{
            data: hasData ? planData : [1],
            backgroundColor: hasData ? planColors : ['#e2e8f0'],
            borderWidth: 0,
            hoverOffset: 6,
        }]
    },
    options: {
        responsive: false,
        cutout: '68%',
        plugins: {
            legend: { display: false },
            tooltip: { enabled: hasData }
        }
    }
});

// ── Gym Growth Bar Chart ────────────────────────────────────────────────────
const gymCtx = document.getElementById('gymGrowthChart').getContext('2d');
new Chart(gymCtx, {
    type: 'bar',
    data: {
        labels: {!! json_encode($gymGrowthLabels) !!},
        datasets: [{
            label: 'New Gyms',
            data: {!! json_encode($gymGrowthData) !!},
            backgroundColor: 'rgba(59,130,246,0.7)',
            borderRadius: 6,
            borderSkipped: false,
        }]
    },
    options: {
        responsive: true,
        plugins: { legend: { display: false } },
        scales: {
            x: { grid: { display: false }, ticks: { color: '#94a3b8', font: { size: 12 } } },
            y: {
                beginAtZero: true,
                grid: { color: 'rgba(0,0,0,.05)' },
                ticks: { color: '#94a3b8', font: { size: 11 }, stepSize: 1 }
            }
        }
    }
});
</script>
@endpush

</x-layouts.super-admin>
