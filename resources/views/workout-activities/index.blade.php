<x-layouts.app>
    <x-slot:title>Workout Activities — {{ config('app.name') }}</x-slot:title>
    <x-slot:header>Workout Activities</x-slot:header>
    <x-slot:topbarTitle>Workout Activities</x-slot:topbarTitle>
    <x-slot:breadcrumb>Home / Workout / Activities</x-slot:breadcrumb>

    @if(session('success'))
    <div class="gh-alert gh-alert-success">{{ session('success') }}</div>
    @endif

    <div class="gh-card">
        <div class="gh-card-header">
            <h3 class="gh-card-title">Exercise Library</h3>
            <a href="{{ route('workout-activities.create') }}" class="gh-btn gh-btn-primary">+ Add Exercise</a>
        </div>
        @if($activities->isEmpty())
        <div class="gh-card-body" style="text-align:center;padding:60px;">
            <div style="font-size:40px;margin-bottom:12px;">🏃</div>
            <h4>No exercises yet</h4>
            <p style="color:#9ca3af;font-size:14px;margin-bottom:16px;">Add exercises to your exercise library.</p>
            <a href="{{ route('workout-activities.create') }}" class="gh-btn gh-btn-primary">+ Add Exercise</a>
        </div>
        @else
        <div class="gh-card-body" style="padding:0;">
            <table class="gh-table">
                <thead><tr><th>Name</th><th>Category</th><th>Muscle Group</th><th>Difficulty</th><th>Equipment</th><th>Actions</th></tr></thead>
                <tbody>
                @foreach($activities as $act)
                <tr>
                    <td style="font-weight:500;">{{ $act->name }}</td>
                    <td style="font-size:13px;color:#6b7280;">{{ $act->category?->name ?? '—' }}</td>
                    <td style="font-size:13px;color:#6b7280;">{{ $act->muscle_group ?? '—' }}</td>
                    <td>
                        @if($act->difficulty==='beginner')<span class="gh-badge gh-badge-success">Beginner</span>
                        @elseif($act->difficulty==='intermediate')<span class="gh-badge gh-badge-warning">Intermediate</span>
                        @elseif($act->difficulty==='advanced')<span class="gh-badge gh-badge-danger">Advanced</span>
                        @else<span class="gh-badge gh-badge-muted">—</span>
                        @endif
                    </td>
                    <td style="font-size:13px;color:#6b7280;">{{ $act->equipment ?? '—' }}</td>
                    <td>
                        <a href="{{ route('workout-activities.edit', $act) }}" class="gh-btn gh-btn-outline gh-btn-sm">Edit</a>
                        <form method="POST" action="{{ route('workout-activities.destroy', $act) }}" style="display:inline;" onsubmit="return confirm('Delete?');">
                            @csrf @method('DELETE')
                            <button class="gh-btn gh-btn-sm" style="background:#fee2e2;color:#dc2626;border:none;cursor:pointer;">Delete</button>
                        </form>
                    </td>
                </tr>
                @endforeach
                </tbody>
            </table>
            <div style="padding:16px;">{{ $activities->links() }}</div>
        </div>
        @endif
    </div>
</x-layouts.app>