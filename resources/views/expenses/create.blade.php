<x-layouts.app>
    <x-slot:title>Add Expense — {{ config('app.name') }}</x-slot:title>
    <x-slot:header>Add Expense</x-slot:header>
    <x-slot:topbarTitle>Add Expense</x-slot:topbarTitle>
    <x-slot:breadcrumb>Home / <a href="{{ route('expenses.index') }}">Expenses</a> / Add</x-slot:breadcrumb>

    <div class="gh-card" style="max-width:600px;">
        <div class="gh-card-header"><h3 class="gh-card-title">Expense Details</h3></div>
        <div class="gh-card-body">
            @if($errors->any())
            <div class="gh-alert gh-alert-danger"><ul style="margin:0;padding-left:18px;">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul></div>
            @endif
            <form method="POST" action="{{ route('expenses.store') }}">
                @csrf
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;">
                    <div style="grid-column:1/-1;">
                        <label class="gh-label">Title *</label>
                        <input type="text" name="title" class="gh-input" value="{{ old('title') }}" required>
                    </div>
                    <div>
                        <label class="gh-label">Amount (₹) *</label>
                        <input type="number" name="amount" class="gh-input" value="{{ old('amount') }}" step="0.01" min="0" required>
                    </div>
                    <div>
                        <label class="gh-label">Date *</label>
                        <input type="date" name="date" class="gh-input" value="{{ old('date', date('Y-m-d')) }}" required>
                    </div>
                    <div style="grid-column:1/-1;">
                        <label class="gh-label">Category</label>
                        <select name="finance_type_id" class="gh-input">
                            <option value="">— Select Category —</option>
                            @foreach($financeTypes as $ft)<option value="{{ $ft->id }}" {{ old('finance_type_id')==$ft->id?'selected':'' }}>{{ $ft->name }}</option>@endforeach
                        </select>
                    </div>
                    <div style="grid-column:1/-1;">
                        <label class="gh-label">Description</label>
                        <textarea name="description" class="gh-input" rows="3">{{ old('description') }}</textarea>
                    </div>
                </div>
                <div style="margin-top:20px;display:flex;gap:10px;">
                    <button type="submit" class="gh-btn gh-btn-primary">Record Expense</button>
                    <a href="{{ route('expenses.index') }}" class="gh-btn gh-btn-outline">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</x-layouts.app>
