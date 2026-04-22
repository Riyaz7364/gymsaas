<x-layouts.app>
    <x-slot:title>Edit Finance Type — {{ config('app.name') }}</x-slot:title>
    <x-slot:header>Edit Finance Type</x-slot:header>
    <x-slot:topbarTitle>Edit Finance Type</x-slot:topbarTitle>
    <x-slot:breadcrumb>Home / <a href="{{ route('finance-types.index') }}">Finance Types</a> / Edit</x-slot:breadcrumb>

    <div class="gh-card" style="max-width:480px;">
        <div class="gh-card-header"><h3 class="gh-card-title">Edit: {{ $financeType->name }}</h3></div>
        <div class="gh-card-body">
            @if($errors->any())
            <div class="gh-alert gh-alert-danger"><ul style="margin:0;padding-left:18px;">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul></div>
            @endif
            <form method="POST" action="{{ route('finance-types.update', $financeType) }}">
                @csrf @method('PUT')
                <div style="display:flex;flex-direction:column;gap:16px;">
                    <div>
                        <label class="gh-label">Name *</label>
                        <input type="text" name="name" class="gh-input" value="{{ old('name', $financeType->name) }}" required>
                    </div>
                    <div>
                        <label class="gh-label">Type *</label>
                        <select name="type" class="gh-input" required>
                            <option value="income" {{ old('type',$financeType->type)==='income'?'selected':'' }}>Income</option>
                            <option value="expense" {{ old('type',$financeType->type)==='expense'?'selected':'' }}>Expense</option>
                        </select>
                    </div>
                    <div>
                        <label class="gh-label">Description</label>
                        <textarea name="description" class="gh-input" rows="2">{{ old('description', $financeType->description) }}</textarea>
                    </div>
                </div>
                <div style="margin-top:20px;display:flex;gap:10px;">
                    <button type="submit" class="gh-btn gh-btn-primary">Save Changes</button>
                    <a href="{{ route('finance-types.index') }}" class="gh-btn gh-btn-outline">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</x-layouts.app>
