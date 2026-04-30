<x-layouts.app>
    <x-slot:title>Body Stats — {{ config('app.name') }}</x-slot:title>
    <x-slot:header>Body Stats</x-slot:header>
    <x-slot:topbarTitle>Body Stats</x-slot:topbarTitle>
    <x-slot:breadcrumb>Home / Health / Body Stats</x-slot:breadcrumb>

    @if(session('success'))
    <div class="gh-alert gh-alert-success">{{ session('success') }}</div>
    @endif

    <div class="gh-card">
        <div class="gh-card-header">
            <h3 class="gh-card-title">Body Measurements</h3>
            <a href="{{ gym_route('gym.body-stats.create') }}" class="gh-btn gh-btn-primary">+ Record Stats</a>
        </div>
        @if($stats->isEmpty())
        <div class="gh-card-body" style="text-align:center;padding:60px;">
            <div style="font-size:40px;margin-bottom:12px;">📊</div>
            <h4>No body stats recorded</h4>
            <p style="color:#9ca3af;font-size:14px;margin-bottom:16px;">Track member body measurements over time.</p>
            <a href="{{ gym_route('gym.body-stats.create') }}" class="gh-btn gh-btn-primary">+ Record Stats</a>
        </div>
        @else
        <div class="gh-card-body" style="padding:0;">
            <table class="gh-table">
                <thead><tr><th>Member</th><th>Date</th><th>Weight</th><th>Height</th><th>BMI</th><th>Body Fat %</th><th>Actions</th></tr></thead>
                <tbody>
                @foreach($stats as $stat)
                <tr>
                    <td style="font-weight:500;">{{ $stat->member?->name ?? '—' }}</td>
                    <td style="font-size:13px;color:#6b7280;">{{ $stat->date?->format('d M Y') ?? '—' }}</td>
                    <td>{{ $stat->weight ?? '—' }} kg</td>
                    <td>{{ $stat->height ?? '—' }} cm</td>
                    <td>
                        @if($stat->bmi)
                        <span style="font-weight:600;color:{{ $stat->bmi < 18.5 ? '#f59e0b' : ($stat->bmi < 25 ? '#22c55e' : ($stat->bmi < 30 ? '#f97316' : '#ef4444')) }};">{{ $stat->bmi }}</span>
                        @else —
                        @endif
                    </td>
                    <td>{{ $stat->body_fat_pct ? $stat->body_fat_pct.'%' : '—' }}</td>
                    <td>
                        <a href="{{ gym_route('gym.body-stats.edit', [$stat]) }}" class="gh-btn gh-btn-outline gh-btn-sm">Edit</a>
                        <form method="POST" action="{{ gym_route('gym.body-stats.destroy', [$stat]) }}" style="display:inline;" onsubmit="return confirm('Delete?');">
                            @csrf @method('DELETE')
                            <button class="gh-btn gh-btn-sm" style="background:#fee2e2;color:#dc2626;border:none;cursor:pointer;">Delete</button>
                        </form>
                    </td>
                </tr>
                @endforeach
                </tbody>
            </table>
            <div style="padding:16px;">{{ $stats->links() }}</div>
        </div>
        @endif
    </div>
</x-layouts.app>