<x-layouts.app>
    <x-slot:title>Add Food Item — {{ config('app.name') }}</x-slot:title>
    <x-slot:header>Add Food Item</x-slot:header>
    <x-slot:topbarTitle>Diet & Nutrition</x-slot:topbarTitle>
    <x-slot:breadcrumb>Home / <a href="{{ route('food-items.index') }}" style="color:var(--gh-primary);text-decoration:none;">Food & Drinks</a> / Add</x-slot:breadcrumb>

    <div class="gh-card" style="max-width:600px;">
        <div class="gh-card-body">
            <form method="POST" action="{{ route('food-items.store') }}">
                @csrf

                <div style="margin-bottom:20px;">
                    <label style="display:block;font-weight:600;font-size:14px;margin-bottom:8px;">Category <span style="color:#ef4444;">*</span></label>
                    <select name="category_id" class="gh-input" required>
                        <option value="">Select a category</option>
                        @foreach($categories as $category)
                        <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                        @endforeach
                    </select>
                    @error('category_id')<div style="color:#ef4444;font-size:13px;">{{ $message }}</div>@enderror
                </div>

                <div style="margin-bottom:20px;">
                    <label style="display:block;font-weight:600;font-size:14px;margin-bottom:8px;">Food Name <span style="color:#ef4444;">*</span></label>
                    <input type="text" name="name" class="gh-input" value="{{ old('name') }}" placeholder="e.g., Chicken Breast" required>
                    @error('name')<div style="color:#ef4444;font-size:13px;">{{ $message }}</div>@enderror
                </div>

                <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;margin-bottom:20px;">
                    <div>
                        <label style="display:block;font-weight:600;font-size:14px;margin-bottom:8px;">Serving Size</label>
                        <input type="text" name="serving_size" class="gh-input" value="{{ old('serving_size') }}" placeholder="e.g., 100">
                        @error('serving_size')<div style="color:#ef4444;font-size:13px;">{{ $message }}</div>@enderror
                    </div>
                    <div>
                        <label style="display:block;font-weight:600;font-size:14px;margin-bottom:8px;">Unit <span style="color:#ef4444;">*</span></label>
                        <select name="serving_unit" class="gh-input" required>
                            <option value="g" {{ old('serving_unit', 'g') == 'g' ? 'selected' : '' }}>Grams (g)</option>
                            <option value="ml" {{ old('serving_unit') == 'ml' ? 'selected' : '' }}>Milliliters (ml)</option>
                            <option value="oz" {{ old('serving_unit') == 'oz' ? 'selected' : '' }}>Ounces (oz)</option>
                            <option value="cup" {{ old('serving_unit') == 'cup' ? 'selected' : '' }}>Cup</option>
                            <option value="tbsp" {{ old('serving_unit') == 'tbsp' ? 'selected' : '' }}>Tablespoon</option>
                            <option value="tsp" {{ old('serving_unit') == 'tsp' ? 'selected' : '' }}>Teaspoon</option>
                            <option value="piece" {{ old('serving_unit') == 'piece' ? 'selected' : '' }}>Piece</option>
                        </select>
                        @error('serving_unit')<div style="color:#ef4444;font-size:13px;">{{ $message }}</div>@enderror
                    </div>
                </div>

                <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;margin-bottom:20px;">
                    <div>
                        <label style="display:block;font-weight:600;font-size:14px;margin-bottom:8px;">Calories</label>
                        <input type="number" name="calories" class="gh-input" value="{{ old('calories') }}" placeholder="0" min="0">
                        @error('calories')<div style="color:#ef4444;font-size:13px;">{{ $message }}</div>@enderror
                    </div>
                    <div>
                        <label style="display:block;font-weight:600;font-size:14px;margin-bottom:8px;">Protein (g)</label>
                        <input type="number" name="protein_g" class="gh-input" value="{{ old('protein_g') }}" placeholder="0.00" min="0" step="0.01">
                        @error('protein_g')<div style="color:#ef4444;font-size:13px;">{{ $message }}</div>@enderror
                    </div>
                </div>

                <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;margin-bottom:20px;">
                    <div>
                        <label style="display:block;font-weight:600;font-size:14px;margin-bottom:8px;">Carbs (g)</label>
                        <input type="number" name="carbs_g" class="gh-input" value="{{ old('carbs_g') }}" placeholder="0.00" min="0" step="0.01">
                        @error('carbs_g')<div style="color:#ef4444;font-size:13px;">{{ $message }}</div>@enderror
                    </div>
                    <div>
                        <label style="display:block;font-weight:600;font-size:14px;margin-bottom:8px;">Fat (g)</label>
                        <input type="number" name="fat_g" class="gh-input" value="{{ old('fat_g') }}" placeholder="0.00" min="0" step="0.01">
                        @error('fat_g')<div style="color:#ef4444;font-size:13px;">{{ $message }}</div>@enderror
                    </div>
                </div>

                <div style="margin-bottom:20px;">
                    <label style="display:block;font-weight:600;font-size:14px;margin-bottom:8px;">Fiber (g)</label>
                    <input type="number" name="fiber_g" class="gh-input" value="{{ old('fiber_g') }}" placeholder="0.00" min="0" step="0.01">
                    @error('fiber_g')<div style="color:#ef4444;font-size:13px;">{{ $message }}</div>@enderror
                </div>

                <div style="display:flex;gap:12px;">
                    <button type="submit" class="gh-btn gh-btn-primary">Save Food Item</button>
                    <a href="{{ route('food-items.index') }}" class="gh-btn gh-btn-outline">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</x-layouts.app>
