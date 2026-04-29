<x-layouts.app>
    <x-slot:title>Finance Overview — {{ config('app.name') }}</x-slot:title>
    <x-slot:header>Finance</x-slot:header>
    <x-slot:topbarTitle>Finance Overview</x-slot:topbarTitle>
    <x-slot:breadcrumb>Home / Finance</x-slot:breadcrumb>

    <!-- Key Metrics -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
        <!-- Total Revenue -->
        <div class="gh-card">
            <div class="gh-card-body">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-500">Total Revenue</p>
                        <h3 class="text-3xl font-bold text-gray-900 mt-2">₹{{ number_format($totalRevenue, 2) }}</h3>
                        <p class="text-sm text-gray-500 mt-1">From successful payments</p>
                    </div>
                    <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center text-2xl">
                        💰
                    </div>
                </div>
            </div>
        </div>

        <!-- Total Expenses -->
        <div class="gh-card">
            <div class="gh-card-body">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-500">Total Expenses</p>
                        <h3 class="text-3xl font-bold text-gray-900 mt-2">₹{{ number_format($totalExpenses, 2) }}</h3>
                        <p class="text-sm text-gray-500 mt-1">Gym operating costs</p>
                    </div>
                    <div class="w-12 h-12 bg-red-100 rounded-lg flex items-center justify-center text-2xl">
                        📉
                    </div>
                </div>
            </div>
        </div>

        <!-- Pending Payments -->
        <div class="gh-card">
            <div class="gh-card-body">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-500">Pending Payments</p>
                        <h3 class="text-3xl font-bold text-gray-900 mt-2">₹{{ number_format($pendingPayments, 2) }}</h3>
                        <p class="text-sm text-gray-500 mt-1">Awaiting processing</p>
                    </div>
                    <div class="w-12 h-12 bg-yellow-100 rounded-lg flex items-center justify-center text-2xl">
                        ⏳
                    </div>
                </div>
            </div>
        </div>

        <!-- Outstanding Invoices -->
        <div class="gh-card">
            <div class="gh-card-body">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-500">Outstanding</p>
                        <h3 class="text-3xl font-bold text-gray-900 mt-2">₹{{ number_format($unpaidInvoices, 2) }}</h3>
                        <p class="text-sm text-gray-500 mt-1">Unpaid invoices balance</p>
                    </div>
                    <div class="w-12 h-12 bg-orange-100 rounded-lg flex items-center justify-center text-2xl">
                        📋
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
        <!-- Invoice Statistics -->
        <div class="gh-card lg:col-span-1">
            <div class="gh-card-header">
                <h3 class="gh-card-title">Invoice Statistics</h3>
            </div>
            <div class="gh-card-body space-y-4">
                <div class="flex items-center justify-between pb-4 border-b">
                    <span class="text-gray-600">Total Invoices</span>
                    <span class="text-2xl font-bold text-gray-900">{{ $invoiceStats['total'] }}</span>
                </div>
                <div class="flex items-center justify-between pb-4 border-b">
                    <span class="text-gray-600">
                        <span class="gh-badge gh-badge-success">Paid</span>
                    </span>
                    <span class="text-2xl font-bold text-green-600">{{ $invoiceStats['paid'] }}</span>
                </div>
                <div class="flex items-center justify-between pb-4 border-b">
                    <span class="text-gray-600">
                        <span class="gh-badge gh-badge-warning">Partial</span>
                    </span>
                    <span class="text-2xl font-bold text-yellow-600">{{ $invoiceStats['partial'] }}</span>
                </div>
                <div class="flex items-center justify-between">
                    <span class="text-gray-600">
                        <span class="gh-badge gh-badge-danger">Unpaid</span>
                    </span>
                    <span class="text-2xl font-bold text-red-600">{{ $invoiceStats['unpaid'] }}</span>
                </div>
            </div>
        </div>

        <!-- Net Profit -->
        <div class="gh-card lg:col-span-2">
            <div class="gh-card-header">
                <h3 class="gh-card-title">Financial Summary</h3>
            </div>
            <div class="gh-card-body">
                <div class="space-y-4">
                    <div class="flex justify-between items-center pb-4 border-b">
                        <span class="text-gray-600 font-medium">Total Revenue</span>
                        <span class="text-2xl font-bold text-green-600">₹{{ number_format($totalRevenue, 2) }}</span>
                    </div>
                    <div class="flex justify-between items-center pb-4 border-b">
                        <span class="text-gray-600 font-medium">Total Expenses</span>
                        <span class="text-2xl font-bold text-red-600">-₹{{ number_format($totalExpenses, 2) }}</span>
                    </div>
                    <div class="flex justify-between items-center pt-2">
                        <span class="text-gray-900 font-semibold">Net Profit</span>
                        <span class="text-3xl font-bold {{ ($totalRevenue - $totalExpenses) >= 0 ? 'text-green-600' : 'text-red-600' }}">
                            ₹{{ number_format($totalRevenue - $totalExpenses, 2) }}
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Recent Payments -->
        <div class="gh-card">
            <div class="gh-card-header">
                <h3 class="gh-card-title">Recent Payments</h3>
                <a href="{{ route('gym.payments', $gym) }}" class="text-blue-600 hover:text-blue-800 text-sm">View All</a>
            </div>
            @if($recentPayments->isEmpty())
            <div class="gh-card-body text-center py-8">
                <p class="text-gray-500">No payments yet</p>
            </div>
            @else
            <div class="gh-card-body" style="padding:0;">
                <table class="gh-table">
                    <thead>
                        <tr>
                            <th>Member</th>
                            <th>Amount</th>
                            <th>Status</th>
                            <th>Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($recentPayments as $payment)
                        <tr>
                            <td>
                                <div class="flex flex-col">
                                    <span class="font-medium">{{ $payment->member?->name ?? '—' }}</span>
                                    @if($payment->razorpay_payment_id)
                                    <span class="text-xs text-gray-500">{{ substr($payment->razorpay_payment_id, 0, 12) }}...</span>
                                    @endif
                                </div>
                            </td>
                            <td class="font-semibold">₹{{ number_format($payment->amount, 2) }}</td>
                            <td>
                                @if($payment->status === 'success')
                                <span class="gh-badge gh-badge-success">Success</span>
                                @elseif($payment->status === 'pending')
                                <span class="gh-badge gh-badge-warning">Pending</span>
                                @else
                                <span class="gh-badge gh-badge-danger">Failed</span>
                                @endif
                            </td>
                            <td class="text-sm text-gray-500">{{ $payment->paid_at?->format('d M Y') }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @endif
        </div>

        <!-- Recent Expenses -->
        <div class="gh-card">
            <div class="gh-card-header">
                <h3 class="gh-card-title">Recent Expenses</h3>
                <a href="{{ route('gym.expenses.index', $gym) }}" class="text-blue-600 hover:text-blue-800 text-sm">View All</a>
            </div>
            @if($recentExpenses->isEmpty())
            <div class="gh-card-body text-center py-8">
                <p class="text-gray-500">No expenses yet</p>
            </div>
            @else
            <div class="gh-card-body" style="padding:0;">
                <table class="gh-table">
                    <thead>
                        <tr>
                            <th>Description</th>
                            <th>Category</th>
                            <th>Amount</th>
                            <th>Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($recentExpenses as $expense)
                        <tr>
                            <td>{{ $expense->description ?? '—' }}</td>
                            <td><span class="text-xs px-2 py-1 bg-gray-100 rounded">{{ $expense->expenseType?->name ?? '—' }}</span></td>
                            <td class="font-semibold text-red-600">₹{{ number_format($expense->amount, 2) }}</td>
                            <td class="text-sm text-gray-500">{{ $expense->created_at?->format('d M Y') }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @endif
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="mt-6">
        <div class="gh-card">
            <div class="gh-card-header">
                <h3 class="gh-card-title">Quick Actions</h3>
            </div>
            <div class="gh-card-body">
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <a href="{{ route('gym.invoices.create', $gym) }}" class="gh-btn gh-btn-outline block text-center">
                        📝 Create Invoice
                    </a>
                    <a href="{{ route('gym.expenses.create', $gym) }}" class="gh-btn gh-btn-outline block text-center">
                        💸 Add Expense
                    </a>
                    <a href="{{ route('gym.invoices.index', $gym) }}" class="gh-btn gh-btn-outline block text-center">
                        📋 View Invoices
                    </a>
                    @if(auth()->user()->gymHasModule('online_payments'))
                    <a href="{{ route('gym.payments', $gym) }}" class="gh-btn gh-btn-outline block text-center">
                        💳 Payment History
                    </a>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-layouts.app>
