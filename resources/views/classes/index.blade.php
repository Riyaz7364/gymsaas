<x-layouts.app>
    <x-slot:title>Gym Classes — {{ config('app.name') }}</x-slot:title>
    <x-slot:header>Gym Classes</x-slot:header>
    <x-slot:topbarTitle>Gym Classes</x-slot:topbarTitle>
    <x-slot:breadcrumb>Home / Classes</x-slot:breadcrumb>

    @if(session('success'))
    <div class="gh-alert gh-alert-success">{{ session('success') }}</div>
    @endif

    <div class="gh-card">
        <div class="gh-card-header">
            <h3 class="gh-card-title">All Classes <span style="color:#9ca3af; font-weight:400; font-size:13px;">({{ $classes->total() }})</span></h3>
            <a href="{{ route('classes.create') }}" class="gh-btn gh-btn-primary gh-btn-sm">+ Add Class</a>
        </div>

        @if($classes->isEmpty())
        <div class="gh-card-body" style="text-align:center; padding:60px;">
            <div style="font-size:40px; margin-bottom:12px;">🏃</div>
            <h4 style="font-weight:600; margin-bottom:6px;">No classes yet</h4>
            <p style="color:#9ca3af; font-size:14px; margin-bottom:16px;">Create your first gym class.</p>
            <a href="{{ route('classes.create') }}" class="gh-btn gh-btn-primary">+ Add Class</a>
        </div>
        @else
        <div class="gh-card-body" style="padding:0;">
            <table class="gh-table">
                <thead><tr><th>Name</th><th>Trainer</th><th>Schedule</th><th>Time</th><th>Capacity</th><th>Status</th><th>Actions</th></tr></thead>
                <tbody>
                @foreach($classes as $class)
                <tr>
                    <td><div style="font-weight:500;">{{ $class->name }}</div><div style="font-size:12px;color:#9ca3af;">{{ $class->room }}</div></td>
                    <td>{{ $class->trainer?->name ?? '—' }}</td>
                    <td><div style="display:flex;gap:4px;flex-wrap:wrap;">@foreach($class->schedule_days ?? [] as $d)<span style="background:#ecfdf5;color:#0abf8e;padding:2px 6px;border-radius:4px;font-size:11px;text-transform:capitalize;">{{ $d }}</span>@endforeach</div></td>
                    <td style="font-size:13px;">{{ $class->start_time ? \Carbon\Carbon::parse($class->start_time)->format('h:i A') : '—' }} – {{ $class->end_time ? \Carbon\Carbon::parse($class->end_time)->format('h:i A') : '' }}</td>
                    <td>{{ $class->capacity }}</td>
                    <td><span class="gh-badge {{ $class->status === 'active' ? 'gh-badge-success' : 'gh-badge-muted' }}">{{ ucfirst($class->status) }}</span></td>
                    <td>
                        <a href="{{ route('classes.edit', $class) }}" class="gh-btn gh-btn-outline gh-btn-sm">Edit</a>
                        <form method="POST" action="{{ route('classes.destroy', $class) }}" style="display:inline;" onsubmit="return confirm('Delete this class?');">
                            @csrf @method('DELETE')
                            <button class="gh-btn gh-btn-sm" style="background:#fee2e2;color:#dc2626;border:none;cursor:pointer;">Delete</button>
                        </form>
                    </td>
                </tr>
                @endforeach
                </tbody>
            </table>
            <div style="padding:16px;">{{ $classes->links() }}</div>
        </div>
        @endif
    </div>
</x-layouts.app>