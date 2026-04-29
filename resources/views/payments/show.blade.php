<x-layouts.app>
    <x-slot:title>Payment Details — {{ config('app.name') }}</x-slot:title>
    <x-slot:header>Payment Details</x-slot:header>
    <x-slot:topbarTitle>Payment Details</x-slot:topbarTitle>
    <x-slot:breadcrumb>Home / Finance / Payment History / Details</x-slot:breadcrumb>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Payment Details -->
        <div class="lg:col-span-2">
            <div class="gh-card">
                <div class="gh-card-header">
                    <h3 class="gh-card-title">Payment Information</h3>
                    <a href="{{ route('payments.index') }}" class="gh-btn gh-btn-outline">← Back to History</a>
                </div>
                <div class="gh-card-body">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <h4 class="font-semibold text-gray-900 mb-4">Basic Details</h4>
                            <dl class="space-y-3">
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Payment ID</dt>
                                    <dd class="text-sm text-gray-900">{{ $payment->razorpay_payment_id ?? $payment->gateway_txn_id ?? '—' }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Order ID</dt>
                                    <dd class="text-sm text-gray-900">{{ $payment->razorpay_order_id ?? $payment->gateway_order_id ?? '—' }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Amount</dt>
                                    <dd class="text-lg font-semibold text-gray-900">₹{{ number_format($payment->amount, 2) }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Method</dt>
                                    <dd class="text-sm text-gray-900">{{ ucfirst(str_replace('_', ' ', $payment->method ?? 'unknown')) }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Status</dt>
                                    <dd>
                                        @if($payment->status === 'success')
                                        <span class="gh-badge gh-badge-success">Success</span>
                                        @elseif($payment->status === 'pending')
                                        <span class="gh-badge gh-badge-warning">Pending</span>
                                        @elseif($payment->status === 'failed')
                                        <span class="gh-badge gh-badge-danger">Failed</span>
                                        @else
                                        <span class="gh-badge gh-badge-secondary">{{ ucfirst($payment->status ?? 'unknown') }}</span>
                                        @endif
                                    </dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Transfer Status</dt>
                                    <dd>
                                        @if($payment->transfer_status === 'completed')
                                        <span class="gh-badge gh-badge-success">Completed</span>
                                        @elseif($payment->transfer_status === 'pending')
                                        <span class="gh-badge gh-badge-warning">Pending</span>
                                        @elseif($payment->transfer_status === 'failed')
                                        <span class="gh-badge gh-badge-danger">Failed</span>
                                        @else
                                        <span class="gh-badge gh-badge-secondary">—</span>
                                        @endif
                                    </dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Transfer ID</dt>
                                    <dd class="text-sm text-gray-900">{{ $payment->transfer_id ?? '—' }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Payment Date</dt>
                                    <dd class="text-sm text-gray-900">{{ $payment->paid_at?->format('d M Y H:i:s') ?? '—' }}</dd>
                                </div>
                            </dl>
                        </div>

                        <div>
                            <h4 class="font-semibold text-gray-900 mb-4">Related Information</h4>
                            <dl class="space-y-3">
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Member</dt>
                                    <dd class="text-sm text-gray-900">
                                        @if($payment->member)
                                        <a href="{{ route('members.show', $payment->member) }}" class="text-blue-600 hover:text-blue-800">
                                            {{ $payment->member->name }}
                                        </a>
                                        @else
                                        —
                                        @endif
                                    </dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Invoice</dt>
                                    <dd class="text-sm text-gray-900">
                                        @if($payment->invoice)
                                        <a href="{{ route('invoices.show', $payment->invoice) }}" class="text-blue-600 hover:text-blue-800">
                                            {{ $payment->invoice->invoice_no }}
                                        </a>
                                        @else
                                        —
                                        @endif
                                    </dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Notes</dt>
                                    <dd class="text-sm text-gray-900">{{ $payment->notes ?? '—' }}</dd>
                                </div>
                            </dl>
                        </div>
                    </div>

                    @if($payment->gateway_response)
                    <div class="mt-6">
                        <h4 class="font-semibold text-gray-900 mb-4">Gateway Response</h4>
                        <pre class="bg-gray-50 p-4 rounded text-xs overflow-x-auto">{{ json_encode($payment->gateway_response, JSON_PRETTY_PRINT) }}</pre>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Actions Sidebar -->
        <div>
            <div class="gh-card">
                <div class="gh-card-header">
                    <h3 class="gh-card-title">Actions</h3>
                </div>
                <div class="gh-card-body space-y-3">
                    @if($payment->invoice)
                    <a href="{{ route('invoices.show', $payment->invoice) }}" class="gh-btn gh-btn-primary w-full">
                        View Invoice
                    </a>
                    @endif

                    @if($payment->member)
                    <a href="{{ route('members.show', $payment->member) }}" class="gh-btn gh-btn-outline w-full">
                        View Member
                    </a>
                    @endif

                    @if($payment->status === 'success' && $payment->transfer_status !== 'completed')
                    <form method="POST" action="{{ route('payments.transfer', $payment) }}" class="w-full">
                        @csrf
                        <button type="submit" class="gh-btn gh-btn-warning w-full" onclick="return confirm('Are you sure you want to retry the transfer?')">
                            Retry Transfer
                        </button>
                    </form>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-layouts.app>