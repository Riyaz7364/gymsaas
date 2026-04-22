<x-layouts.app>
    <x-slot:title>Edit Invoice — {{ config('app.name') }}</x-slot:title>
    <x-slot:header>Edit Invoice</x-slot:header>
    <x-slot:topbarTitle>Edit Invoice</x-slot:topbarTitle>
    <x-slot:breadcrumb>Home / <a href="{{ route('invoices.index') }}">Invoices</a> / Edit</x-slot:breadcrumb>

    <div class="gh-card" style="max-width:640px;">
        <div class="gh-card-header"><h3 class="gh-card-title">Edit Invoice — {{ $invoice->invoice_no }}</h3></div>
        <div class="gh-card-body">
            @if($errors->any())
            <div class="gh-alert gh-alert-danger"><ul style="margin:0;padding-left:18px;">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul></div>
            @endif
            <form method="POST" action="{{ route('invoices.update', $invoice) }}">
                @csrf @method('PUT')
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;">
                    <div style="grid-column:1/-1;">
                        <label class="gh-label">Member *</label>
                        <select name="member_id" class="gh-input" required>
                            <option value="">— Select Member —</option>
                            @foreach($members as $m)<option value="{{ $m->id }}" {{ old('member_id',$invoice->member_id)==$m->id?'selected':'' }}>{{ $m->name }} ({{ $m->member_id }})</option>@endforeach
                        </select>
                    </div>
                    <div>
                        <label class="gh-label">Subtotal (₹) *</label>
                        <input type="number" name="subtotal" class="gh-input" value="{{ old('subtotal', $invoice->subtotal) }}" step="0.01" min="0" required>
                    </div>
                    <div>
                        <label class="gh-label">Tax (₹)</label>
                        <input type="number" name="tax" class="gh-input" value="{{ old('tax', $invoice->tax) }}" step="0.01" min="0">
                    </div>
                    <div>
                        <label class="gh-label">Discount (₹)</label>
                        <input type="number" name="discount" class="gh-input" value="{{ old('discount', $invoice->discount) }}" step="0.01" min="0">
                    </div>
                    <div>
                        <label class="gh-label">Status</label>
                        <select name="status" class="gh-input">
                            @foreach(['unpaid','partial','paid'] as $s)<option value="{{ $s }}" {{ old('status',$invoice->status)===$s?'selected':'' }}>{{ ucfirst($s) }}</option>@endforeach
                        </select>
                    </div>
                    <div>
                        <label class="gh-label">Due Date</label>
                        <input type="date" name="due_date" class="gh-input" value="{{ old('due_date', $invoice->due_date?->format('Y-m-d')) }}">
                    </div>
                    <div style="grid-column:1/-1;">
                        <label class="gh-label">Notes</label>
                        <textarea name="notes" class="gh-input" rows="2">{{ old('notes', $invoice->notes) }}</textarea>
                    </div>
                </div>
                <div style="margin-top:20px;display:flex;gap:10px;">
                    <button type="submit" class="gh-btn gh-btn-primary">Save Changes</button>
                    <a href="{{ route('invoices.index') }}" class="gh-btn gh-btn-outline">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</x-layouts.app>