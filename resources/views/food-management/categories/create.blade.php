<x-layouts.app>
    <x-slot:title>Add Food Category — {{ config('app.name') }}</x-slot:title>
    <x-slot:header>Add Food Category</x-slot:header>
    <x-slot:topbarTitle>Diet & Nutrition</x-slot:topbarTitle>
    <x-slot:breadcrumb>Home / <a href="{{ route('food-categories.index') }}" style="color:var(--gh-primary);text-decoration:none;">Categories</a> / Add</x-slot:breadcrumb>

    <div class="gh-card" style="max-width:600px;">
        <div class="gh-card-body">
            <form method="POST" action="{{ route('food-categories.store') }}">
                @csrf

                <div style="margin-bottom:20px;">
                    <label style="display:block;font-weight:600;font-size:14px;margin-bottom:8px;">Category Name <span style="color:#ef4444;">*</span></label>
                    <input type="text" name="name" class="gh-input" value="{{ old('name') }}" placeholder="e.g., Meat, Drinks, Vegetables" required>
                    @error('name')<div style="color:#ef4444;font-size:13px;">{{ $message }}</div>@enderror
                </div>

                <div style="margin-bottom:20px;">
                    <label style="display:block;font-weight:600;font-size:14px;margin-bottom:8px;">Description</label>
                    <textarea name="description" class="gh-input" placeholder="Optional description" rows="4" style="resize:vertical;">{{ old('description') }}</textarea>
                    @error('description')<div style="color:#ef4444;font-size:13px;">{{ $message }}</div>@enderror
                </div>

                <div style="margin-bottom:20px;">
                    <label style="display:block;font-weight:600;font-size:14px;margin-bottom:8px;">Icon</label>
                    <input type="text" name="icon" class="gh-input" value="{{ old('icon') }}" placeholder="e.g., 🍗 (emoji)">
                    @error('icon')<div style="color:#ef4444;font-size:13px;">{{ $message }}</div>@enderror
                </div>

                <div style="display:flex;gap:12px;">
                    <button type="submit" class="gh-btn gh-btn-primary">Create Category</button>
                    <a href="{{ route('food-categories.index') }}" class="gh-btn gh-btn-outline">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</x-layouts.app>
