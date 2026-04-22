<x-layouts.app>
    <x-slot:title>Edit Category — {{ config('app.name') }}</x-slot:title>
    <x-slot:header>Edit Category</x-slot:header>
    <x-slot:topbarTitle>Edit Category</x-slot:topbarTitle>
    <x-slot:breadcrumb>Home / Workout / Categories / Edit</x-slot:breadcrumb>

    <div class="gh-card" style="max-width:600px;">
        <div class="gh-card-header">
            <h3 class="gh-card-title">Edit: {{ $category->name }}</h3>
        </div>
        <div class="gh-card-body">
            @if($errors->any())
            <div class="gh-alert gh-alert-danger">
                <ul style="margin:0;padding-left:18px;">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
            </div>
            @endif
            <form method="POST" action="{{ route('categories.update', $category) }}">
                @csrf @method('PUT')
                <div style="margin-bottom:16px;">
                    <label class="gh-label">Name <span style="color:#ef4444;">*</span></label>
                    <input class="gh-input" type="text" name="name" value="{{ old('name', $category->name) }}" required>
                </div>
                <div style="margin-bottom:16px;">
                    <label class="gh-label">Icon (emoji)</label>
                    <input class="gh-input" type="text" name="icon" value="{{ old('icon', $category->icon) }}" placeholder="e.g. 🏋️">
                </div>
                <div style="margin-bottom:24px;">
                    <label class="gh-label">Description</label>
                    <textarea class="gh-input" name="description" rows="3" style="resize:vertical;">{{ old('description', $category->description) }}</textarea>
                </div>
                <div style="display:flex;gap:12px;">
                    <button type="submit" class="gh-btn gh-btn-primary">Save Changes</button>
                    <a href="{{ route('categories.index') }}" class="gh-btn gh-btn-outline">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</x-layouts.app>
