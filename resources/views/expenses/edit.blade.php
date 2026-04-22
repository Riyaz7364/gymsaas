<x-layouts.app>
    <x-slot:title>Edit Expense — {{ config('app.name') }}</x-slot:title>
    <x-slot:header>Edit Expense</x-slot:header>
    <x-slot:topbarTitle>Edit Expense</x-slot:topbarTitle>
    <x-slot:breadcrumb>Home / <a href="{{ route('expenses.index') }}">Expenses</a> / Edit</x-slot:breadcrumb>

    <div class="gh-card" style="max-width:600px;">
        <div class="gh-card-header"><h3 class="gh-card-title">Edit Expense</h3></div>
        <div class="gh-card-body">
            @if($errors->any())
            <div class="gh-alert gh-alert-danger"><ul style="margin:0;padding-left:18px;">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul></div>
            @endif
            <form method="POST" action="{{ route('expenses.update', $expense) }}">
                @csrf @method('PUT')
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;">
                    <div style="grid-column:1/-1;">
                        <label class="gh-label">Title *</label>
                        <input type="text" name="title" class="gh-input" value="{{ old('title', $expense->title) }}" required>
                    </div>
                    <div>
                        <label class="gh-label">Amount (₹) *</label>
                        <input type="number" name="amount" class="gh-input" value="{{ old('amount', $expense->amount) }}" step="0.01" min="0" required>
                    </div>
                    <div>
                        <label class="gh-label">Date *</label>
                        <input type="date" name="date" class="gh-input" value="{{ old('date', $expense->date->format('Y-m-d')) }}" required>
                    </div>
                    <div style="grid-column:1/-1;">
                        <label class="gh-label">Category</label>
                        <select name="finance_type_id" class="gh-input">
                            <option value="">— Select Category —</option>
                            @foreach($financeTypes as $ft)<option value="{{ $ft->id }}" {{ old('finance_type_id',$expense->finance_type_id)==$ft->id?'selected':'' }}>{{ $ft->name }}</option>@endforeach
                        </select>
                    </div>
                    <div style="grid-column:1/-1;">
                        <label class="gh-label">Description</label>
                        <textarea name="description" class="gh-input" rows="3">{{ old('description', $expense->description) }}</textarea>
                    </div>
                </div>
                <div style="margin-top:20px;display:flex;gap:10px;">
                    <button type="submit" class="gh-btn gh-btn-primary">Save Changes</button>
                    <a href="{{ route('expenses.index') }}" class="gh-btn gh-btn-outline">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</x-layouts.app>
