<x-layouts.app>
    <x-slot:title>Payment History — {{ config('app.name') }}</x-slot:title>
    <x-slot:header>Payment History</x-slot:header>
    <x-slot:topbarTitle>Payment History</x-slot:topbarTitle>
    <x-slot:breadcrumb>Home / Finance / Payment History</x-slot:breadcrumb>

    @if(session('success'))
    <div class="gh-alert gh-alert-success">{{ session('success') }}</div>
    @endif

    <div class="gh-card">
        <div class="gh-card-header">
            <h3 class="gh-card-title">Payment History</h3>
            <div class="flex gap-2">
                <button onclick="document.getElementById('filters').classList.toggle('hidden')" class="gh-btn gh-btn-outline">Filters</button>
            </div>
        </div>

        <!-- Filters -->
        <div id="filters" class="gh-card-body hidden" style="border-bottom:1px solid #e5e7eb;">
            <form method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                    <select name="status" class="gh-input">
                        <option value="">All Status</option>
                        @foreach($statuses as $status)
                        <option value="{{ $status }}" {{ request('status') === $status ? 'selected' : '' }}>{{ ucfirst($status) }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Method</label>
                    <select name="method" class="gh-input">
                        <option value="">All Methods</option>
                        @foreach($methods as $method)
                        <option value="{{ $method }}" {{ request('method') === $method ? 'selected' : '' }}>{{ ucfirst(str_replace('_', ' ', $method)) }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Member</label>
                    <select name="member_id" class="gh-input">
                        <option value="">All Members</option>
                        @foreach($members as $member)
                        <option value="{{ $member->id }}" {{ request('member_id') == $member->id ? 'selected' : '' }}>{{ $member->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Date Range</label>
                    <div class="flex gap-2">
                        <input type="date" name="from_date" value="{{ request('from_date') }}" class="gh-input flex-1">
                        <input type="date" name="to_date" value="{{ request('to_date') }}" class="gh-input flex-1">
                    </div>
                </div>
                <div class="md:col-span-4 flex gap-2">
                    <button type="submit" class="gh-btn gh-btn-primary">Apply Filters</button>
                    <a href="{{ route('payments.index') }}" class="gh-btn gh-btn-outline">Clear</a>
                </div>
            </form>
        </div>

        @if($payments->isEmpty())
        <div class="gh-card-body" style="text-align:center;padding:60px;">
            <div style="font-size:40px;margin-bottom:12px;">💳</div>
            <h4>No payments yet</h4>
            <p style="color:#9ca3af;font-size:14px;margin-bottom:16px;">Payment history will appear here once members make payments.</p>
        </div>
        @else
        <div class="gh-card-body" style="padding:0;">
            <table class="gh-table">
                <thead>
                    <tr>
                        <th>Payment ID</th>
                        <th>Member</th>
                        <th>Amount</th>
                        <th>Method</th>
                        <th>Status</th>
                        <th>Transfer Status</th>
                        <th>Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                @foreach($payments as $payment)
                <tr>
                    <td style="font-weight:600;color:#6366f1;">
                        {{ $payment->razorpay_payment_id ?? $payment->gateway_txn_id ?? '—' }}
                    </td>
                    <td>{{ $payment->member?->name ?? '—' }}</td>
                    <td style="font-weight:600;">₹{{ number_format($payment->amount, 2) }}</td>
                    <td>{{ ucfirst(str_replace('_', ' ', $payment->method ?? 'unknown')) }}</td>
                    <td>
                        @if($payment->status === 'success')
                        <span class="gh-badge gh-badge-success">Success</span>
                        @elseif($payment->status === 'pending')
                        <span class="gh-badge gh-badge-warning">Pending</span>
                        @elseif($payment->status === 'failed')
                        <span class="gh-badge gh-badge-danger">Failed</span>
                        @else
                        <span class="gh-badge gh-badge-secondary">{{ ucfirst($payment->status ?? 'unknown') }}</span>
                        @endif
                    </td>
                    <td>
                        @if($payment->transfer_status === 'completed')
                        <span class="gh-badge gh-badge-success">Completed</span>
                        @elseif($payment->transfer_status === 'pending')
                        <span class="gh-badge gh-badge-warning">Pending</span>
                        @elseif($payment->transfer_status === 'failed')
                        <span class="gh-badge gh-badge-danger">Failed</span>
                        @else
                        <span class="gh-badge gh-badge-secondary">—</span>
                        @endif
                    </td>
                    <td style="font-size:13px;color:#6b7280;">{{ $payment->paid_at?->format('d M Y H:i') ?? '—' }}</td>
                    <td>
                        <a href="{{ route('payments.show', $payment) }}" class="gh-btn gh-btn-outline gh-btn-sm">View</a>
                    </td>
                </tr>
                @endforeach
                </tbody>
            </table>
        </div>

        @if($payments->hasPages())
        <div class="gh-card-footer">
            {{ $payments->links() }}
        </div>
        @endif
        @endif
    </div>
</x-layouts.app>