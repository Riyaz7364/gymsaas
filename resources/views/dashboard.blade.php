<x-layouts.app>
    <x-slot:title>Dashboard - {{ config('app.name') }}</x-slot:title>
    <x-slot:header>Dashboard</x-slot:header>
    <x-slot:topbarTitle>Dashboard</x-slot:topbarTitle>
    <x-slot:breadcrumb>
        <span style="color:#0abf8e;">Home</span> / Dashboard
    </x-slot:breadcrumb>

    <div style="display:grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap:20px; margin-bottom:24px;">
        @module('members_management')
        <div class="gh-stat-card">
            <div class="gh-stat-icon" style="background: #ecfdf5; color: #0abf8e;">
                <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z" /></svg>
            </div>
            <div class="gh-stat-info">
                <span class="gh-stat-label">Total Members</span>
                <span class="gh-stat-value" id="stat-members">{{ $stats['total_members'] ?? 0 }}</span>
            </div>
            <div class="gh-stat-badge {{ ($stats['members_change'] ?? 0) >= 0 ? 'gh-stat-badge-up' : 'gh-stat-badge-down' }}">
                {{ ($stats['members_change'] ?? 0) >= 0 ? '+' : '' }}{{ $stats['members_change'] ?? '0' }}%
            </div>
        </div>
        @endmodule

        @module('attendance_management')
        <div class="gh-stat-card">
            <div class="gh-stat-icon" style="background: #eff6ff; color: #3b82f6;">
                <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
            </div>
            <div class="gh-stat-info">
                <span class="gh-stat-label">Today's Attendance</span>
                <span class="gh-stat-value">{{ $stats['today_attendance'] ?? 0 }}</span>
            </div>
            <div class="gh-stat-badge gh-stat-badge-neutral">Today</div>
        </div>
        @endmodule

        @module('finance_management')
        <div class="gh-stat-card">
            <div class="gh-stat-icon" style="background: #fff7ed; color: #f97316;">
                <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 8.25h19.5M2.25 9h19.5m-16.5 5.25h6m-6 2.25h3m-3.75 3h15a2.25 2.25 0 002.25-2.25V6.75A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25v10.5A2.25 2.25 0 004.5 19.5z" /></svg>
            </div>
            <div class="gh-stat-info">
                <span class="gh-stat-label">Monthly Revenue</span>
                <span class="gh-stat-value">{{ auth()->user()->gym?->currency ?? 'Rs' }}{{ number_format($stats['monthly_revenue'] ?? 0) }}</span>
            </div>
            <div class="gh-stat-badge {{ ($stats['revenue_change'] ?? 0) >= 0 ? 'gh-stat-badge-up' : 'gh-stat-badge-down' }}">
                {{ ($stats['revenue_change'] ?? 0) >= 0 ? '+' : '' }}{{ $stats['revenue_change'] ?? '0' }}%
            </div>
        </div>
        @endmodule

        @module('trainers_management')
        <div class="gh-stat-card">
            <div class="gh-stat-icon" style="background: #fdf4ff; color: #a855f7;">
                <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M17.982 18.725A7.488 7.488 0 0012 15.75a7.488 7.488 0 00-5.982 2.975m11.963 0a9 9 0 10-11.963 0m11.963 0A8.966 8.966 0 0112 21a8.966 8.966 0 01-5.982-2.275M15 9.75a3 3 0 11-6 0 3 3 0 016 0z" /></svg>
            </div>
            <div class="gh-stat-info">
                <span class="gh-stat-label">Active Trainers</span>
                <span class="gh-stat-value">{{ $stats['active_trainers'] ?? 0 }}</span>
            </div>
            <div class="gh-stat-badge gh-stat-badge-neutral">Staff</div>
        </div>
        @endmodule

        @module('membership_management')
        <div class="gh-stat-card">
            <div class="gh-stat-icon" style="background: #fef2f2; color: #ef4444;">
                <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z" /></svg>
            </div>
            <div class="gh-stat-info">
                <span class="gh-stat-label">Expiring Soon</span>
                <span class="gh-stat-value">{{ $stats['expiring_soon'] ?? 0 }}</span>
            </div>
            <div class="gh-stat-badge gh-stat-badge-warning">7 days</div>
        </div>
        @endmodule

        @module('event_management')
        <div class="gh-stat-card">
            <div class="gh-stat-icon" style="background: #ecfdf5; color: #10b981;">
                <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5" /></svg>
            </div>
            <div class="gh-stat-info">
                <span class="gh-stat-label">Classes Today</span>
                <span class="gh-stat-value">{{ $stats['classes_today'] ?? 0 }}</span>
            </div>
            <div class="gh-stat-badge gh-stat-badge-neutral">Scheduled</div>
        </div>
        @endmodule
    </div>

    <div style="display:grid; grid-template-columns: 2fr 1fr; gap:20px; margin-bottom:24px;">
        @module('finance_management')
        <div class="gh-card">
            <div class="gh-card-header">
                <h3 class="gh-card-title">Revenue Overview</h3>
                <span style="font-size:13px; color:#6b7280;">Last 12 months</span>
            </div>
            <div class="gh-card-body">
                <div id="revenueChart" style="height:280px;"></div>
            </div>
        </div>
        @endmodule

        @module('members_management')
        <div class="gh-card">
            <div class="gh-card-header">
                <h3 class="gh-card-title">Member Status</h3>
            </div>
            <div class="gh-card-body">
                <div id="memberStatusChart" style="height:280px;"></div>
            </div>
        </div>
        @endmodule
    </div>

    <div style="display:grid; grid-template-columns: 1fr 1fr; gap:20px;">
        @module('members_management')
        <div class="gh-card">
            <div class="gh-card-header">
                <h3 class="gh-card-title">Recent Members</h3>
                <a href="{{ gym_route('gym.members.index') }}" style="font-size:13px; color:#0abf8e; text-decoration:none;">View All -></a>
            </div>
            <div class="gh-card-body" style="padding:0;">
                <table class="gh-table">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Plan</th>
                            <th>Status</th>
                            <th>Joined</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($recentMembers ?? [] as $member)
                        <tr>
                            <td>
                                <div style="display:flex; align-items:center; gap:10px;">
                                    <img src="{{ $member->avatar_url ?? 'https://ui-avatars.com/api/?name='.urlencode($member->name).'&color=fff&background=0abf8e&size=32' }}" class="gh-avatar-sm" alt="">
                                    <div>
                                        <div style="font-weight:500; font-size:14px;">{{ $member->name }}</div>
                                        <div style="font-size:12px; color:#9ca3af;">{{ $member->phone }}</div>
                                    </div>
                                </div>
                            </td>
                            <td>{{ $member->activePlan?->plan?->name ?? '-' }}</td>
                            <td>
                                <span class="gh-badge {{ match($member->status) {
                                    'active' => 'gh-badge-success',
                                    'frozen' => 'gh-badge-info',
                                    'expired' => 'gh-badge-danger',
                                    default => 'gh-badge-warning'
                                } }}">{{ ucfirst($member->status) }}</span>
                            </td>
                            <td style="font-size:13px; color:#6b7280;">{{ $member->created_at->format('d M') }}</td>
                        </tr>
                        @empty
                        <tr><td colspan="4" style="text-align:center; color:#9ca3af; padding:24px;">No members yet.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @endmodule

        @module('attendance_management')
        <div class="gh-card">
            <div class="gh-card-header">
                <h3 class="gh-card-title">Today's Attendance</h3>
                <a href="{{ gym_route('gym.attendance.index') }}" style="font-size:13px; color:#0abf8e; text-decoration:none;">View All -></a>
            </div>
            <div class="gh-card-body" style="padding:0;">
                <table class="gh-table">
                    <thead>
                        <tr>
                            <th>Member</th>
                            <th>Check-In</th>
                            <th>Check-Out</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($todayAttendance ?? [] as $att)
                        <tr>
                            <td>
                                <div style="font-weight:500; font-size:14px;">{{ $att->member?->name ?? '-' }}</div>
                            </td>
                            <td style="font-size:13px; color:#10b981;">{{ $att->check_in ? \Carbon\Carbon::parse($att->check_in)->format('h:i A') : '-' }}</td>
                            <td style="font-size:13px; color:#6b7280;">{{ $att->check_out ? \Carbon\Carbon::parse($att->check_out)->format('h:i A') : 'Active' }}</td>
                        </tr>
                        @empty
                        <tr><td colspan="3" style="text-align:center; color:#9ca3af; padding:24px;">No check-ins today.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @endmodule
    </div>

    @push('scripts')
    <script>
    document.addEventListener('DOMContentLoaded', function () {
        const revenueChartElement = document.querySelector('#revenueChart');
        const memberStatusChartElement = document.querySelector('#memberStatusChart');

        if (revenueChartElement) {
            const revenueOptions = {
                chart: { type: 'area', height: 280, toolbar: { show: false }, sparkline: { enabled: false } },
                series: [{ name: 'Revenue', data: {!! json_encode($monthlyRevenueData ?? array_fill(0, 12, 0)) !!} }],
                xaxis: {
                    categories: {!! json_encode($monthlyLabels ?? ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec']) !!},
                    labels: { style: { fontSize: '12px', colors: '#9ca3af' } },
                    axisBorder: { show: false }, axisTicks: { show: false }
                },
                yaxis: { labels: { style: { fontSize: '12px', colors: '#9ca3af' }, formatter: v => 'Rs ' + (v >= 1000 ? (v / 1000).toFixed(0) + 'k' : v) } },
                colors: ['#0abf8e'],
                fill: { type: 'gradient', gradient: { shadeIntensity: 1, opacityFrom: 0.4, opacityTo: 0.05, stops: [0, 100] } },
                stroke: { curve: 'smooth', width: 2 },
                grid: { borderColor: '#f3f4f6', strokeDashArray: 4 },
                tooltip: { y: { formatter: v => 'Rs ' + Number(v).toLocaleString('en-IN') } },
                dataLabels: { enabled: false }
            };

            new ApexCharts(revenueChartElement, revenueOptions).render();
        }

        if (memberStatusChartElement) {
            const statusOptions = {
                chart: { type: 'donut', height: 280 },
                series: {!! json_encode(array_values($memberStatusCounts ?? ['active' => 0, 'frozen' => 0, 'expired' => 0, 'inactive' => 0])) !!},
                labels: {!! json_encode(array_keys($memberStatusCounts ?? ['active' => 0, 'frozen' => 0, 'expired' => 0, 'inactive' => 0])) !!},
                colors: ['#0abf8e', '#3b82f6', '#ef4444', '#9ca3af'],
                legend: { position: 'bottom', fontSize: '13px' },
                dataLabels: { enabled: true, formatter: (val) => Math.round(val) + '%' },
                plotOptions: { pie: { donut: { size: '65%', labels: { show: true, total: { show: true, label: 'Total', fontSize: '14px', color: '#374151' } } } } }
            };

            new ApexCharts(memberStatusChartElement, statusOptions).render();
        }
    });
    </script>
    @endpush
</x-layouts.app>
