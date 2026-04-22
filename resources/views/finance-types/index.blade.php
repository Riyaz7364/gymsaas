<x-layouts.app>
    <x-slot:title>Finance Types — {{ config('app.name') }}</x-slot:title>
    <x-slot:header>Finance Types</x-slot:header>
    <x-slot:topbarTitle>Finance Types</x-slot:topbarTitle>
    <x-slot:breadcrumb>Home / Finance / Finance Types</x-slot:breadcrumb>

    @if(session('success'))
    <div class="gh-alert gh-alert-success">{{ session('success') }}</div>
    @endif

    <div class="gh-card">
        <div class="gh-card-header">
            <h3 class="gh-card-title">Finance Types</h3>
            <a href="{{ route('finance-types.create') }}" class="gh-btn gh-btn-primary">+ Add Type</a>
        </div>
        @if($types->isEmpty())
        <div class="gh-card-body" style="text-align:center;padding:60px;">
            <div style="font-size:40px;margin-bottom:12px;">🏷️</div>
            <h4>No finance types yet</h4>
            <p style="color:#9ca3af;font-size:14px;margin-bottom:16px;">Create income/expense categories to organise your finances.</p>
            <a href="{{ route('finance-types.create') }}" class="gh-btn gh-btn-primary">+ Add Type</a>
        </div>
        @else
        <div class="gh-card-body" style="padding:0;">
            <table class="gh-table">
                <thead><tr><th>Name</th><th>Type</th><th>Expenses</th><th>Actions</th></tr></thead>
                <tbody>
                @foreach($types as $type)
                <tr>
                    <td style="font-weight:500;">{{ $type->name }}</td>
                    <td>
                        @if($type->type === 'income')
                        <span class="gh-badge gh-badge-success">Income</span>
                        @else
                        <span class="gh-badge gh-badge-danger">Expense</span>
                        @endif
                    </td>
                    <td style="font-size:13px;color:#6b7280;">{{ $type->expenses_count }}</td>
                    <td>
                        <a href="{{ route('finance-types.edit', $type) }}" class="gh-btn gh-btn-outline gh-btn-sm">Edit</a>
                        <form method="POST" action="{{ route('finance-types.destroy', $type) }}" style="display:inline;" onsubmit="return confirm('Delete this type?');">
                            @csrf @method('DELETE')
                            <button class="gh-btn gh-btn-sm" style="background:#fee2e2;color:#dc2626;border:none;cursor:pointer;">Delete</button>
                        </form>
                    </td>
                </tr>
                @endforeach
                </tbody>
            </table>
        </div>
        @endif
    </div>
</x-layouts.app>