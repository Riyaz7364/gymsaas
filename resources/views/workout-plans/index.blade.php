<x-layouts.app>
    <x-slot:title>Workout Plans — {{ config('app.name') }}</x-slot:title>
    <x-slot:header>Workout Plans</x-slot:header>
    <x-slot:topbarTitle>Workout Plans</x-slot:topbarTitle>
    <x-slot:breadcrumb>Home / Workout / Plans</x-slot:breadcrumb>

    @if(session('success'))
    <div class="gh-alert gh-alert-success">{{ session('success') }}</div>
    @endif

    <div class="gh-card">
        <div class="gh-card-header">
            <h3 class="gh-card-title">Workout Plans</h3>
            <a href="{{ route('workout-plans.create') }}" class="gh-btn gh-btn-primary">+ New Plan</a>
        </div>
        @if($plans->isEmpty())
        <div class="gh-card-body" style="text-align:center;padding:60px;">
            <div style="font-size:40px;margin-bottom:12px;">💪</div>
            <h4>No workout plans yet</h4>
            <p style="color:#9ca3af;font-size:14px;margin-bottom:16px;">Create personalised workout plans for your members.</p>
            <a href="{{ route('workout-plans.create') }}" class="gh-btn gh-btn-primary">+ New Plan</a>
        </div>
        @else
        <div class="gh-card-body" style="padding:0;">
            <table class="gh-table">
                <thead><tr><th>Name</th><th>Member</th><th>Trainer</th><th>Status</th><th>Default</th><th>Actions</th></tr></thead>
                <tbody>
                @foreach($plans as $plan)
                <tr>
                    <td style="font-weight:500;">{{ $plan->name }}</td>
                    <td style="font-size:13px;color:#6b7280;">{{ $plan->member?->name ?? '—' }}</td>
                    <td style="font-size:13px;color:#6b7280;">{{ $plan->trainer?->name ?? '—' }}</td>
                    <td>
                        @if($plan->is_active)
                        <span class="gh-badge gh-badge-success">Active</span>
                        @else
                        <span class="gh-badge gh-badge-muted">Inactive</span>
                        @endif
                    </td>
                    <td>
                        @if($plan->is_default)
                        <span class="gh-badge gh-badge-info">Default</span>
                        @endif
                    </td>
                    <td>
                        <a href="{{ route('workout-plans.edit', $plan) }}" class="gh-btn gh-btn-outline gh-btn-sm">Edit</a>
                        <form method="POST" action="{{ route('workout-plans.destroy', $plan) }}" style="display:inline;" onsubmit="return confirm('Delete plan?');">
                            @csrf @method('DELETE')
                            <button class="gh-btn gh-btn-sm" style="background:#fee2e2;color:#dc2626;border:none;cursor:pointer;">Delete</button>
                        </form>
                    </td>
                </tr>
                @endforeach
                </tbody>
            </table>
            <div style="padding:16px;">{{ $plans->links() }}</div>
        </div>
        @endif
    </div>
</x-layouts.app>