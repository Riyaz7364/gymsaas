<x-layouts.app>
    <x-slot:title>New Invoice — {{ config('app.name') }}</x-slot:title>
    <x-slot:header>New Invoice</x-slot:header>
    <x-slot:topbarTitle>New Invoice</x-slot:topbarTitle>
    <x-slot:breadcrumb>Home / <a href="{{ route('invoices.index') }}">Invoices</a> / New</x-slot:breadcrumb>

    <div class="gh-card" style="max-width:640px;">
        <div class="gh-card-header"><h3 class="gh-card-title">Invoice Details</h3></div>
        <div class="gh-card-body">
            @if($errors->any())
            <div class="gh-alert gh-alert-danger"><ul style="margin:0;padding-left:18px;">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul></div>
            @endif
            <form method="POST" action="{{ route('invoices.store') }}">
                @csrf
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;">
                    <div style="grid-column:1/-1;">
                        <label class="gh-label">Member *</label>
                        <select name="member_id" class="gh-input" required>
                            <option value="">— Select Member —</option>
                            @foreach($members as $m)<option value="{{ $m->id }}" {{ old('member_id')==$m->id?'selected':'' }}>{{ $m->name }} ({{ $m->member_id }})</option>@endforeach
                        </select>
                    </div>
                    <div>
                        <label class="gh-label">Subtotal (₹) *</label>
                        <input type="number" name="subtotal" id="subtotal" class="gh-input" value="{{ old('subtotal', 0) }}" step="0.01" min="0" required oninput="calcTotal()">
                    </div>
                    <div>
                        <label class="gh-label">Tax (₹)</label>
                        <input type="number" name="tax" id="tax" class="gh-input" value="{{ old('tax', 0) }}" step="0.01" min="0" oninput="calcTotal()">
                    </div>
                    <div>
                        <label class="gh-label">Discount (₹)</label>
                        <input type="number" name="discount" id="discount" class="gh-input" value="{{ old('discount', 0) }}" step="0.01" min="0" oninput="calcTotal()">
                    </div>
                    <div>
                        <label class="gh-label">Total (₹)</label>
                        <input type="number" name="total_display" id="total_display" class="gh-input" value="0" readonly style="background:#f9fafb;">
                    </div>
                    <div>
                        <label class="gh-label">Due Date</label>
                        <input type="date" name="due_date" class="gh-input" value="{{ old('due_date') }}">
                    </div>
                    <div style="grid-column:1/-1;">
                        <label class="gh-label">Notes</label>
                        <textarea name="notes" class="gh-input" rows="2">{{ old('notes') }}</textarea>
                    </div>
                </div>
                <div style="margin-top:20px;display:flex;gap:10px;">
                    <button type="submit" class="gh-btn gh-btn-primary">Create Invoice</button>
                    <a href="{{ route('invoices.index') }}" class="gh-btn gh-btn-outline">Cancel</a>
                </div>
            </form>
        </div>
    </div>
    <script>
    function calcTotal() {
        var s = parseFloat(document.getElementById('subtotal').value)||0;
        var t = parseFloat(document.getElementById('tax').value)||0;
        var d = parseFloat(document.getElementById('discount').value)||0;
        document.getElementById('total_display').value = (s+t-d).toFixed(2);
    }
    calcTotal();
    </script>
</x-layouts.app>