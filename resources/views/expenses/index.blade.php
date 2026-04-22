<x-layouts.app>
    <x-slot:title>Expenses — {{ config('app.name') }}</x-slot:title>
    <x-slot:header>Expenses</x-slot:header>
    <x-slot:topbarTitle>Expenses</x-slot:topbarTitle>
    <x-slot:breadcrumb>Home / Finance / Expenses</x-slot:breadcrumb>

    @if(session('success'))
    <div class="gh-alert gh-alert-success">{{ session('success') }}</div>
    @endif

    <div class="gh-card" style="margin-bottom:20px;">
        <div class="gh-card-body">
            <div style="display:flex;align-items:center;gap:16px;">
                <div>
                    <div style="font-size:12px;color:#9ca3af;">This Month's Expenses</div>
                    <div style="font-size:24px;font-weight:700;color:#ef4444;">₹{{ number_format($total, 2) }}</div>
                </div>
                <div style="margin-left:auto;">
                    <a href="{{ route('expenses.create') }}" class="gh-btn gh-btn-primary">+ Add Expense</a>
                </div>
            </div>
        </div>
    </div>

    <div class="gh-card">
        <div class="gh-card-header"><h3 class="gh-card-title">All Expenses</h3></div>
        @if($expenses->isEmpty())
        <div class="gh-card-body" style="text-align:center;padding:60px;">
            <div style="font-size:40px;margin-bottom:12px;">💸</div>
            <h4>No expenses recorded</h4>
            <p style="color:#9ca3af;font-size:14px;margin-bottom:16px;">Track your gym's expenses here.</p>
            <a href="{{ route('expenses.create') }}" class="gh-btn gh-btn-primary">+ Add Expense</a>
        </div>
        @else
        <div class="gh-card-body" style="padding:0;">
            <table class="gh-table">
                <thead><tr><th>Title</th><th>Category</th><th>Amount</th><th>Date</th><th>Actions</th></tr></thead>
                <tbody>
                @foreach($expenses as $expense)
                <tr>
                    <td style="font-weight:500;">{{ $expense->title }}</td>
                    <td style="font-size:13px;color:#6b7280;">{{ $expense->financeType?->name ?? '—' }}</td>
                    <td style="font-weight:600;color:#ef4444;">₹{{ number_format($expense->amount, 2) }}</td>
                    <td style="font-size:13px;color:#6b7280;">{{ $expense->date->format('d M Y') }}</td>
                    <td>
                        <a href="{{ route('expenses.edit', $expense) }}" class="gh-btn gh-btn-outline gh-btn-sm">Edit</a>
                        <form method="POST" action="{{ route('expenses.destroy', $expense) }}" style="display:inline;" onsubmit="return confirm('Delete expense?');">
                            @csrf @method('DELETE')
                            <button class="gh-btn gh-btn-sm" style="background:#fee2e2;color:#dc2626;border:none;cursor:pointer;">Delete</button>
                        </form>
                    </td>
                </tr>
                @endforeach
                </tbody>
            </table>
            <div style="padding:16px;">{{ $expenses->links() }}</div>
        </div>
        @endif
    </div>
</x-layouts.app>