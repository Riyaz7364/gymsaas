<x-layouts.app>
    <x-slot:title>Food Items — {{ config('app.name') }}</x-slot:title>
    <x-slot:header>Food & Drinks</x-slot:header>
    <x-slot:topbarTitle>Diet & Nutrition</x-slot:topbarTitle>
    <x-slot:breadcrumb>Home / <a href="{{ gym_route('gym.food-items.index') }}" style="color:var(--gh-primary);text-decoration:none;">Food & Drinks</a></x-slot:breadcrumb>

    @if(session('success'))
    <div class="gh-alert gh-alert-success">{{ session('success') }}</div>
    @endif

    {{-- Filter by category --}}
    <div style="margin-bottom:16px;display:flex;gap:8px;flex-wrap:wrap;">
        <a href="{{ gym_route('gym.food-items.index') }}" class="gh-btn {{ !request('category') ? 'gh-btn-primary' : 'gh-btn-outline' }}">All Foods</a>
        @foreach($categories as $category)
        <a href="{{ gym_route('gym.food-items.index', ['category' => $category->id]) }}" class="gh-btn {{ request('category') == $category->id ? 'gh-btn-primary' : 'gh-btn-outline' }}">{{ $category->name }}</a>
        @endforeach
        <a href="{{ gym_route('gym.food-categories.index') }}" class="gh-btn gh-btn-outline">Manage Categories</a>
    </div>

    <div class="gh-card">
        <div class="gh-card-header">
            <h3 class="gh-card-title">Food & Drinks Library</h3>
            <a href="{{ gym_route('gym.food-items.create') }}" class="gh-btn gh-btn-primary">+ Add Food Item</a>
        </div>
        @if($foodItems->isEmpty())
        <div class="gh-card-body" style="text-align:center;padding:60px;">
            <div style="font-size:40px;margin-bottom:12px;">🍎</div>
            <h4>No food items yet</h4>
            <p style="color:#9ca3af;font-size:14px;margin-bottom:16px;">Create a library of food and drink items for your diet plans.</p>
            <a href="{{ gym_route('gym.food-items.create') }}" class="gh-btn gh-btn-primary">+ Add Food Item</a>
        </div>
        @else
        <div class="gh-card-body" style="padding:0;">
            <table class="gh-table">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Category</th>
                        <th>Serving</th>
                        <th>Calories</th>
                        <th>Protein</th>
                        <th>Carbs</th>
                        <th>Fat</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                @foreach($foodItems as $item)
                <tr>
                    <td style="font-weight:500;">{{ $item->name }}</td>
                    <td style="font-size:13px;color:#6b7280;">{{ $item->category?->name ?? '—' }}</td>
                    <td style="font-size:13px;color:#6b7280;">{{ $item->serving_size ?? '—' }} {{ $item->serving_unit }}</td>
                    <td style="font-size:13px;color:#6b7280;">{{ $item->calories ?? '—' }} cal</td>
                    <td style="font-size:13px;color:#6b7280;">{{ $item->protein_g ?? '—' }}g</td>
                    <td style="font-size:13px;color:#6b7280;">{{ $item->carbs_g ?? '—' }}g</td>
                    <td style="font-size:13px;color:#6b7280;">{{ $item->fat_g ?? '—' }}g</td>
                    <td>
                        <a href="{{ gym_route('gym.food-items.edit', [$gym, $item]) }}" class="gh-btn gh-btn-outline gh-btn-sm">Edit</a>
                        <form method="POST" action="{{ gym_route('gym.food-items.destroy', [$item]) }}" style="display:inline;" onsubmit="return confirm('Delete this food item?');">
                            @csrf @method('DELETE')
                            <button class="gh-btn gh-btn-sm" style="background:#fee2e2;color:#dc2626;border:none;cursor:pointer;">Delete</button>
                        </form>
                    </td>
                </tr>
                @endforeach
                </tbody>
            </table>
            <div style="padding:16px;">{{ $foodItems->links() }}</div>
        </div>
        @endif
    </div>
</x-layouts.app>
