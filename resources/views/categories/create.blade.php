<x-layouts.app>
    <x-slot:title>Add Category — {{ config('app.name') }}</x-slot:title>
    <x-slot:header>Add Category</x-slot:header>
    <x-slot:topbarTitle>Add Category</x-slot:topbarTitle>
    <x-slot:breadcrumb>Home / <a href="{{ route('categories.index') }}">Categories</a> / Add</x-slot:breadcrumb>

    <div class="gh-card" style="max-width:480px;">
        <div class="gh-card-header"><h3 class="gh-card-title">Category Details</h3></div>
        <div class="gh-card-body">
            @if($errors->any())
            <div class="gh-alert gh-alert-danger"><ul style="margin:0;padding-left:18px;">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul></div>
            @endif
            <form method="POST" action="{{ route('categories.store') }}">
                @csrf
                <div style="display:flex;flex-direction:column;gap:16px;">
                    <div>
                        <label class="gh-label">Name *</label>
                        <input type="text" name="name" class="gh-input" value="{{ old('name') }}" required>
                    </div>
                    <div>
                        <label class="gh-label">Icon (emoji)</label>
                        <input type="text" name="icon" class="gh-input" value="{{ old('icon') }}" placeholder="🏋️" maxlength="10">
                    </div>
                    <div>
                        <label class="gh-label">Description</label>
                        <textarea name="description" class="gh-input" rows="2">{{ old('description') }}</textarea>
                    </div>
                </div>
                <div style="margin-top:20px;display:flex;gap:10px;">
                    <button type="submit" class="gh-btn gh-btn-primary">Add Category</button>
                    <a href="{{ route('categories.index') }}" class="gh-btn gh-btn-outline">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</x-layouts.app>