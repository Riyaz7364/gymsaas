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
            <h3 class="gh-card-title">Categories</h3>
            <a href="{{ route('categories.create') }}" class="gh-btn gh-btn-primary">+ Add Category</a>
        </div>
        @if($categories->isEmpty())
        <div class="gh-card-body" style="text-align:center;padding:60px;">
            <div style="font-size:40px;margin-bottom:12px;">🏋️</div>
            <h4>No categories yet</h4>
            <p style="color:#9ca3af;font-size:14px;margin-bottom:16px;">Create workout categories to organise exercises.</p>
            <a href="{{ route('categories.create') }}" class="gh-btn gh-btn-primary">+ Add Category</a>
        </div>
        @else
        <div class="gh-card-body" style="padding:0;">
            <table class="gh-table">
                <thead><tr><th>Icon</th><th>Name</th><th>Activities</th><th>Actions</th></tr></thead>
                <tbody>
                @foreach($categories as $cat)
                <tr>
                    <td style="font-size:22px;">{{ $cat->icon ?? '🏃' }}</td>
                    <td style="font-weight:500;">{{ $cat->name }}</td>
                    <td style="font-size:13px;color:#6b7280;">{{ $cat->activities_count }}</td>
                    <td>
                        <a href="{{ route('categories.edit', $cat) }}" class="gh-btn gh-btn-outline gh-btn-sm">Edit</a>
                        <form method="POST" action="{{ route('categories.destroy', $cat) }}" style="display:inline;" onsubmit="return confirm('Delete category?');">
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
