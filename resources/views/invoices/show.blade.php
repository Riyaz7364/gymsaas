<x-layouts.app>
    <x-slot:title>Invoice {{ $invoice->invoice_no }} — {{ config('app.name') }}</x-slot:title>
    <x-slot:header>Invoice {{ $invoice->invoice_no }}</x-slot:header>
    <x-slot:topbarTitle>Invoice {{ $invoice->invoice_no }}</x-slot:topbarTitle>
    <x-slot:breadcrumb>Home / Finance / Invoices / {{ $invoice->invoice_no }}</x-slot:breadcrumb>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Invoice Details -->
        <div class="lg:col-span-2">
            <div class="gh-card">
                <div class="gh-card-header">
                    <h3 class="gh-card-title">Invoice Details</h3>
                    <div class="flex gap-2">
                        <a href="{{ gym_route('gym.invoices.pdf', $invoice) }}" target="_blank" class="gh-btn gh-btn-primary">
                            📄 Download PDF
                        </a>
                        <a href="{{ gym_route('gym.invoices.edit', $invoice) }}" class="gh-btn gh-btn-outline">Edit</a>
                        <a href="{{ gym_route('gym.invoices.index') }}" class="gh-btn gh-btn-outline">← Back</a>
                    </div>
                </div>
                <div class="gh-card-body">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                        <div>
                            <h4 class="font-semibold text-gray-900 mb-4">Invoice Information</h4>
                            <dl class="space-y-3">
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Invoice Number</dt>
                                    <dd class="text-sm font-mono text-gray-900">{{ $invoice->invoice_no }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Status</dt>
                                    <dd>
                                        @if($invoice->status === 'paid')
                                        <span class="gh-badge gh-badge-success">Paid</span>
                                        @elseif($invoice->status === 'partial')
                                        <span class="gh-badge gh-badge-warning">Partial</span>
                                        @else
                                        <span class="gh-badge gh-badge-danger">Unpaid</span>
                                        @endif
                                    </dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Created Date</dt>
                                    <dd class="text-sm text-gray-900">{{ $invoice->created_at->format('d M Y H:i') }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Due Date</dt>
                                    <dd class="text-sm text-gray-900">{{ $invoice->due_date?->format('d M Y') ?? '—' }}</dd>
                                </div>
                            </dl>
                        </div>

                        <div>
                            <h4 class="font-semibold text-gray-900 mb-4">Member Details</h4>
                            <dl class="space-y-3">
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Name</dt>
                                    <dd class="text-sm text-gray-900">
                                        <a href="{{ gym_route('gym.members.show', $invoice->member) }}" class="text-blue-600 hover:text-blue-800">
                                            {{ $invoice->member->name }}
                                        </a>
                                    </dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Email</dt>
                                    <dd class="text-sm text-gray-900">{{ $invoice->member->email }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Phone</dt>
                                    <dd class="text-sm text-gray-900">{{ $invoice->member->phone }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Membership Plan</dt>
                                    <dd class="text-sm text-gray-900">{{ $invoice->memberPlan->plan->name ?? '—' }}</dd>
                                </div>
                            </dl>
                        </div>
                    </div>

                    <!-- Invoice Items -->
                    <div class="border-t pt-6">
                        <h4 class="font-semibold text-gray-900 mb-4">Invoice Items</h4>
                        <div class="overflow-x-auto">
                            <table class="gh-table">
                                <thead>
                                    <tr>
                                        <th>Description</th>
                                        <th class="text-right">Amount</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>Membership Fee - {{ $invoice->memberPlan->plan->name ?? 'General Membership' }}</td>
                                        <td class="text-right font-semibold">₹{{ number_format($invoice->subtotal, 2) }}</td>
                                    </tr>
                                    @if($invoice->tax > 0)
                                    <tr>
                                        <td>Tax</td>
                                        <td class="text-right">₹{{ number_format($invoice->tax, 2) }}</td>
                                    </tr>
                                    @endif
                                    @if($invoice->discount > 0)
                                    <tr>
                                        <td>Discount</td>
                                        <td class="text-right text-green-600">-₹{{ number_format($invoice->discount, 2) }}</td>
                                    </tr>
                                    @endif
                                </tbody>
                                <tfoot>
                                    <tr class="border-t-2">
                                        <td class="font-semibold">Total</td>
                                        <td class="text-right font-bold text-lg">₹{{ number_format($invoice->total, 2) }}</td>
                                    </tr>
                                    <tr>
                                        <td class="font-semibold">Amount Paid</td>
                                        <td class="text-right text-green-600">₹{{ number_format($invoice->amount_paid, 2) }}</td>
                                    </tr>
                                    <tr class="border-t">
                                        <td class="font-semibold">Balance Due</td>
                                        <td class="text-right {{ $invoice->balance_due > 0 ? 'text-red-600' : 'text-green-600' }} font-bold">
                                            ₹{{ number_format($invoice->balance_due, 2) }}
                                        </td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>

                    @if($invoice->notes)
                    <div class="border-t pt-6 mt-6">
                        <h4 class="font-semibold text-gray-900 mb-2">Notes</h4>
                        <p class="text-sm text-gray-700">{{ $invoice->notes }}</p>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Payments Sidebar -->
        <div>
            <div class="gh-card">
                <div class="gh-card-header">
                    <h3 class="gh-card-title">Payment History</h3>
                </div>
                <div class="gh-card-body">
                    @if($invoice->payments->count() > 0)
                    <div class="space-y-4">
                        @foreach($invoice->payments as $payment)
                        <div class="border rounded-lg p-4">
                            <div class="flex justify-between items-start mb-2">
                                <span class="font-medium">₹{{ number_format($payment->amount, 2) }}</span>
                                @if($payment->status === 'success')
                                <span class="gh-badge gh-badge-success">Paid</span>
                                @elseif($payment->status === 'pending')
                                <span class="gh-badge gh-badge-warning">Pending</span>
                                @else
                                <span class="gh-badge gh-badge-danger">Failed</span>
                                @endif
                            </div>
                            <div class="text-sm text-gray-600 space-y-1">
                                <p>Method: {{ ucfirst(str_replace('_', ' ', $payment->method ?? 'unknown')) }}</p>
                                <p>Date: {{ $payment->paid_at?->format('d M Y H:i') ?? '—' }}</p>
                                @if($payment->razorpay_payment_id)
                                <p class="font-mono text-xs">ID: {{ $payment->razorpay_payment_id }}</p>
                                @endif
                            </div>
                        </div>
                        @endforeach
                    </div>
                    @else
                    <p class="text-gray-500 text-center py-8">No payments recorded yet</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-layouts.app>