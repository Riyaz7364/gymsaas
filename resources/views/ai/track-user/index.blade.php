<x-layouts.app>
    <x-slot:title>AI Track User - {{ config('app.name') }}</x-slot:title>
    <x-slot:header>AI Track User</x-slot:header>
    <x-slot:topbarTitle>AI Track User</x-slot:topbarTitle>
    <x-slot:breadcrumb>Home / <span style="color:#0abf8e;">AI Track User</span></x-slot:breadcrumb>

    <div style="display:grid; grid-template-columns: repeat(auto-fit, minmax(170px, 1fr)); gap:14px; margin-bottom:18px;">
        <div class="gh-card"><div class="gh-card-body"><div style="font-size:12px;color:#6b7280;">Tracked Members</div><div style="font-size:24px;font-weight:700;">{{ $summary['total'] }}</div></div></div>
        <div class="gh-card"><div class="gh-card-body"><div style="font-size:12px;color:#6b7280;">High Risk</div><div style="font-size:24px;font-weight:700;color:#dc2626;">{{ $summary['high'] }}</div></div></div>
        <div class="gh-card"><div class="gh-card-body"><div style="font-size:12px;color:#6b7280;">Medium Risk</div><div style="font-size:24px;font-weight:700;color:#d97706;">{{ $summary['medium'] }}</div></div></div>
        <div class="gh-card"><div class="gh-card-body"><div style="font-size:12px;color:#6b7280;">Low Risk</div><div style="font-size:24px;font-weight:700;color:#059669;">{{ $summary['low'] }}</div></div></div>
        <div class="gh-card"><div class="gh-card-body"><div style="font-size:12px;color:#6b7280;">Offer Candidates</div><div style="font-size:24px;font-weight:700;color:#0abf8e;">{{ $summary['offer_candidates'] }}</div></div></div>
    </div>

    <div class="gh-card" style="margin-bottom:18px;">
        <div class="gh-card-body" style="display:flex;justify-content:space-between;gap:14px;flex-wrap:wrap;">
            <div>
                <div style="font-size:12px;color:#6b7280;">Top Performing Trainer</div>
                @if($topTrainer)
                    <div style="font-size:16px;font-weight:700;">{{ $topTrainer->name }}</div>
                    <div style="font-size:13px;color:#6b7280;">{{ $topTrainer->members_count }} members • Rating {{ number_format((float) ($topTrainer->reviews_avg_rating ?? 0), 1) }}/5</div>
                @else
                    <div style="font-size:14px;color:#6b7280;">No trainer data available yet.</div>
                @endif
            </div>
            <div style="max-width:460px;font-size:12px;color:#6b7280;">
                AI score is based on attendance drop, last visit gap, plan expiry, dues status, and member lifecycle state. Each row shows exact reasons for the risk.
            </div>
        </div>
    </div>

    <div class="gh-card" style="margin-bottom:18px;">
        <div class="gh-card-body">
            <form method="GET" style="display:flex;gap:12px;flex-wrap:wrap;">
                <input name="search" value="{{ request('search') }}" class="gh-input" style="max-width:280px;" placeholder="Search member name, phone, member no">
                <select name="risk" class="gh-input" style="max-width:180px;">
                    <option value="">All Risk Levels</option>
                    <option value="high" {{ request('risk') === 'high' ? 'selected' : '' }}>High</option>
                    <option value="medium" {{ request('risk') === 'medium' ? 'selected' : '' }}>Medium</option>
                    <option value="low" {{ request('risk') === 'low' ? 'selected' : '' }}>Low</option>
                </select>
                <button type="submit" class="gh-btn gh-btn-primary">Apply</button>
                @if(request('search') || request('risk'))
                    <a href="{{ gym_route('gym.ai-track-user.index') }}" class="gh-btn gh-btn-outline">Clear</a>
                @endif
            </form>
        </div>
    </div>

    <div class="gh-card">
        <div class="gh-card-body" style="padding:0;">
            <table class="gh-table">
                <thead>
                    <tr>
                        <th>Member</th>
                        <th>Risk</th>
                        <th>Offer</th>
                        <th>Why AI Thinks This</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($insights as $row)
                        @php
                            $member = $row['member'];
                            $riskClass = $row['risk_level'] === 'high' ? 'gh-badge-danger' : ($row['risk_level'] === 'medium' ? 'gh-badge-warning' : 'gh-badge-success');
                        @endphp
                        <tr>
                            <td>
                                <div style="font-weight:600;">{{ $member->name }}</div>
                                <div style="font-size:12px;color:#6b7280;">{{ $member->member_no }} • {{ $member->phone }}</div>
                                <div style="font-size:12px;color:#6b7280;">
                                    Last visit:
                                    {{ $row['days_since_last_visit'] === null ? 'No attendance yet' : $row['days_since_last_visit'].' day(s) ago' }}
                                </div>
                            </td>
                            <td>
                                <span class="gh-badge {{ $riskClass }}">{{ strtoupper($row['risk_level']) }}</span>
                                <div style="margin-top:6px;font-size:13px;">Score: <strong>{{ $row['risk_score'] }}/100</strong></div>
                                <div style="font-size:12px;color:#6b7280;">30d Visits: {{ $row['attendance_30_count'] }}</div>
                            </td>
                            <td>
                                <div style="font-size:13px;"><strong>{{ $row['offer_type'] }}</strong></div>
                                <div style="font-size:12px;color:#6b7280;">Best time: {{ $row['best_offer_time'] }}</div>
                                <div style="font-size:12px;color:#6b7280;">
                                    @if($row['can_send_offer'])
                                        WhatsApp ready
                                    @else
                                        Not ready for auto-send
                                    @endif
                                </div>
                            </td>
                            <td>
                                <ul style="margin:0; padding-left:16px; color:#374151; font-size:13px;">
                                    @foreach($row['reasons'] as $reason)
                                        <li style="margin-bottom:4px;">{{ $reason }}</li>
                                    @endforeach
                                </ul>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" style="text-align:center;color:#9ca3af;padding:28px;">No members matched this filter.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($insights->hasPages())
            <div style="padding:16px 20px; border-top:1px solid var(--gh-border); display:flex; justify-content:space-between; align-items:center;">
                <span style="font-size:13px; color:#6b7280;">
                    Showing {{ $insights->firstItem() }}-{{ $insights->lastItem() }} of {{ $insights->total() }}
                </span>
                {{ $insights->links() }}
            </div>
        @endif
    </div>
</x-layouts.app>

