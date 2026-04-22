<x-layouts.app>
    <x-slot:title>Workout Categories — {{ config('app.name') }}</x-slot:title>
    <x-slot:header>Workout Categories</x-slot:header>
    <x-slot:topbarTitle>Workout Categories</x-slot:topbarTitle>
    <x-slot:breadcrumb>Home / Workout / Categories</x-slot:breadcrumb>

    @if(session('success'))
    <div class="gh-alert gh-alert-success">{{ session('success') }}</div>
    @endif

    <div class="gh-card">
        <div class="gh-card-header">
            <h3 class="gh-card-title">Workout Categories</h3>
            <a href="{{ route('workout-categories.create') }}" class="gh-btn gh-btn-primary">+ Add Category</a>
        </div>
        @if($categories->isEmpty())
        <div class="gh-card-body" style="text-align:center;padding:60px;">
            <div style="font-size:40px;margin-bottom:12px;">🏷️</div>
            <h4>No workout categories yet</h4>
            <a href="{{ route('workout-categories.create') }}" class="gh-btn gh-btn-primary">+ Add Category</a>
        </div>
        @else
        <div class="gh-card-body" style="padding:0;">
            <table class="gh-table">
                <thead><tr><th>Icon</th><th>Name</th><th>Activities</th><th>Actions</th></tr></thead>
                <tbody>
                @foreach($categories as $cat)
                <tr>
                    <td style="font-size:22px;">{{ $cat->icon ?? '🏋️' }}</td>
                    <td style="font-weight:500;">{{ $cat->name }}</td>
                    <td style="font-size:13px;color:#6b7280;">{{ $cat->activities_count }}</td>
                    <td>
                        <a href="{{ route('workout-categories.edit', $cat) }}" class="gh-btn gh-btn-outline gh-btn-sm">Edit</a>
                        <form method="POST" action="{{ route('workout-categories.destroy', $cat) }}" style="display:inline;" onsubmit="return confirm('Delete?');">
                            @csrf @method('DELETE')
                            <button class="gh-btn gh-btn-sm" style="background:#fee2e2;color:#dc2626;border:none;cursor:pointer;">Delete</button>
                        </form>
                    </td>
                </tr>
                @endforeach
                </tbody>
            </table>
        </div>
        @endif
    </div>
</x-layouts.app>
