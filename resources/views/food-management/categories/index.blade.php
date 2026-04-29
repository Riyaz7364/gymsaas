<x-layouts.app>
    <x-slot:title>Food Categories — {{ config('app.name') }}</x-slot:title>
    <x-slot:header>Food Categories</x-slot:header>
    <x-slot:topbarTitle>Diet & Nutrition</x-slot:topbarTitle>
    <x-slot:breadcrumb>Home / <a href="{{ route('food-categories.index') }}" style="color:var(--gh-primary);text-decoration:none;">Categories</a></x-slot:breadcrumb>

    @if(session('success'))
    <div class="gh-alert gh-alert-success">{{ session('success') }}</div>
    @endif

    <div class="gh-card">
        <div class="gh-card-header">
            <h3 class="gh-card-title">Food Categories</h3>
            <a href="{{ route('food-categories.create') }}" class="gh-btn gh-btn-primary">+ New Category</a>
        </div>
        @if($categories->isEmpty())
        <div class="gh-card-body" style="text-align:center;padding:60px;">
            <div style="font-size:40px;margin-bottom:12px;">📂</div>
            <h4>No categories yet</h4>
            <p style="color:#9ca3af;font-size:14px;margin-bottom:16px;">Create food categories to organize your food library.</p>
            <a href="{{ route('food-categories.create') }}" class="gh-btn gh-btn-primary">+ New Category</a>
        </div>
        @else
        <div class="gh-card-body" style="padding:0;">
            <table class="gh-table">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Description</th>
                        <th>Foods</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                @foreach($categories as $category)
                <tr>
                    <td style="font-weight:500;">{{ $category->name }}</td>
                    <td style="font-size:13px;color:#6b7280;">{{ $category->description ?? '—' }}</td>
                    <td style="font-size:13px;color:#6b7280;"><span class="gh-badge">{{ $category->food_items_count }}</span></td>
                    <td>
                        <a href="{{ route('food-categories.edit', $category) }}" class="gh-btn gh-btn-outline gh-btn-sm">Edit</a>
                        <form method="POST" action="{{ route('food-categories.destroy', $category) }}" style="display:inline;" onsubmit="return confirm('Delete this category? Associated food items will not be deleted.');">
                            @csrf @method('DELETE')
                            <button class="gh-btn gh-btn-sm" style="background:#fee2e2;color:#dc2626;border:none;cursor:pointer;">Delete</button>
                        </form>
                    </td>
                </tr>
                @endforeach
                </tbody>
            </table>
            <div style="padding:16px;">{{ $categories->links() }}</div>
        </div>
        @endif
    </div>
</x-layouts.app>
