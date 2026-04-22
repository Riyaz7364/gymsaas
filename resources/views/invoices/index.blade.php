<x-layouts.app>
    <x-slot:title>Invoices — {{ config('app.name') }}</x-slot:title>
    <x-slot:header>Invoices</x-slot:header>
    <x-slot:topbarTitle>Invoices</x-slot:topbarTitle>
    <x-slot:breadcrumb>Home / Finance / Invoices</x-slot:breadcrumb>

    @if(session('success'))
    <div class="gh-alert gh-alert-success">{{ session('success') }}</div>
    @endif

    <div class="gh-card">
        <div class="gh-card-header">
            <h3 class="gh-card-title">All Invoices</h3>
            <a href="{{ route('invoices.create') }}" class="gh-btn gh-btn-primary">+ New Invoice</a>
        </div>
        @if($invoices->isEmpty())
        <div class="gh-card-body" style="text-align:center;padding:60px;">
            <div style="font-size:40px;margin-bottom:12px;">🧾</div>
            <h4>No invoices yet</h4>
            <p style="color:#9ca3af;font-size:14px;margin-bottom:16px;">Create invoices for your members.</p>
            <a href="{{ route('invoices.create') }}" class="gh-btn gh-btn-primary">+ New Invoice</a>
        </div>
        @else
        <div class="gh-card-body" style="padding:0;">
            <table class="gh-table">
                <thead><tr><th>Invoice #</th><th>Member</th><th>Total</th><th>Balance Due</th><th>Status</th><th>Due Date</th><th>Actions</th></tr></thead>
                <tbody>
                @foreach($invoices as $inv)
                <tr>
                    <td style="font-weight:600;color:#6366f1;">{{ $inv->invoice_no }}</td>
                    <td>{{ $inv->member?->name ?? '—' }}</td>
                    <td style="font-weight:600;">₹{{ number_format($inv->total, 2) }}</td>
                    <td style="color:{{ $inv->balance_due > 0 ? '#ef4444' : '#22c55e' }};">₹{{ number_format($inv->balance_due, 2) }}</td>
                    <td>
                        @if($inv->status === 'paid')
                        <span class="gh-badge gh-badge-success">Paid</span>
                        @elseif($inv->status === 'partial')
                        <span class="gh-badge gh-badge-warning">Partial</span>
                        @else
                        <span class="gh-badge gh-badge-danger">Unpaid</span>
                        @endif
                    </td>
                    <td style="font-size:13px;color:#6b7280;">{{ $inv->due_date?->format('d M Y') ?? '—' }}</td>
                    <td>
                        <a href="{{ route('invoices.show', $inv) }}" class="gh-btn gh-btn-outline gh-btn-sm">View</a>
                        <a href="{{ route('invoices.edit', $inv) }}" class="gh-btn gh-btn-outline gh-btn-sm">Edit</a>
                        <form method="POST" action="{{ route('invoices.destroy', $inv) }}" style="display:inline;" onsubmit="return confirm('Delete invoice?');">
                            @csrf @method('DELETE')
                            <button class="gh-btn gh-btn-sm" style="background:#fee2e2;color:#dc2626;border:none;cursor:pointer;">Delete</button>
                        </form>
                    </td>
                </tr>
                @endforeach
                </tbody>
            </table>
            <div style="padding:16px;">{{ $invoices->links() }}</div>
        </div>
        @endif
    </div>
</x-layouts.app>