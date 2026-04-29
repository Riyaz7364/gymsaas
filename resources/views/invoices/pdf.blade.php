<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Invoice {{ $invoice->invoice_no }}</title>
    <style>
        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 12px;
            color: #333;
            line-height: 1.4;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #333;
            padding-bottom: 20px;
        }
        .header h1 {
            margin: 0;
            font-size: 24px;
            color: #333;
        }
        .header p {
            margin: 5px 0;
            color: #666;
        }
        .invoice-details {
            display: table;
            width: 100%;
            margin-bottom: 30px;
        }
        .invoice-details .left, .invoice-details .right {
            display: table-cell;
            width: 50%;
            vertical-align: top;
        }
        .invoice-details h3 {
            margin: 0 0 10px 0;
            font-size: 14px;
            color: #333;
        }
        .invoice-details p {
            margin: 3px 0;
        }
        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }
        .items-table th, .items-table td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        .items-table th {
            background-color: #f5f5f5;
            font-weight: bold;
        }
        .items-table .amount {
            text-align: right;
        }
        .totals {
            width: 300px;
            margin-left: auto;
            margin-top: 20px;
        }
        .totals table {
            width: 100%;
            border-collapse: collapse;
        }
        .totals table td {
            padding: 5px;
            border: none;
        }
        .totals table .label {
            text-align: left;
            font-weight: bold;
        }
        .totals table .amount {
            text-align: right;
            font-weight: bold;
        }
        .totals table .total {
            border-top: 2px solid #333;
            font-size: 16px;
        }
        .footer {
            margin-top: 40px;
            text-align: center;
            font-size: 10px;
            color: #666;
        }
        .status {
            display: inline-block;
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 10px;
            font-weight: bold;
            text-transform: uppercase;
        }
        .status-paid {
            background-color: #d4edda;
            color: #155724;
        }
        .status-unpaid {
            background-color: #f8d7da;
            color: #721c24;
        }
        .status-partial {
            background-color: #fff3cd;
            color: #856404;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>INVOICE</h1>
        <p>Invoice Number: {{ $invoice->invoice_no }}</p>
        <p>Date: {{ $invoice->created_at->format('d M Y') }}</p>
        <p>Due Date: {{ $invoice->due_date?->format('d M Y') ?? 'N/A' }}</p>
        <span class="status status-{{ $invoice->status }}">
            {{ ucfirst($invoice->status) }}
        </span>
    </div>

    <div class="invoice-details">
        <div class="left">
            <h3>From</h3>
            <p><strong>{{ auth()->user()->gym->name ?? 'GymHub' }}</strong></p>
            <p>{{ auth()->user()->gym->address ?? '' }}</p>
            <p>{{ auth()->user()->gym->phone ?? '' }}</p>
            <p>{{ auth()->user()->gym->email ?? '' }}</p>
        </div>
        <div class="right">
            <h3>Bill To</h3>
            <p><strong>{{ $invoice->member->name }}</strong></p>
            <p>{{ $invoice->member->email }}</p>
            <p>{{ $invoice->member->phone }}</p>
            @if($invoice->member->address)
            <p>{{ $invoice->member->address }}</p>
            @endif
        </div>
    </div>

    <table class="items-table">
        <thead>
            <tr>
                <th>Description</th>
                <th class="amount">Amount</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>Membership Fee - {{ $invoice->memberPlan->plan->name ?? 'General Membership' }}</td>
                <td class="amount">₹{{ number_format($invoice->subtotal, 2) }}</td>
            </tr>
            @if($invoice->tax > 0)
            <tr>
                <td>Tax</td>
                <td class="amount">₹{{ number_format($invoice->tax, 2) }}</td>
            </tr>
            @endif
            @if($invoice->discount > 0)
            <tr>
                <td>Discount</td>
                <td class="amount">-₹{{ number_format($invoice->discount, 2) }}</td>
            </tr>
            @endif
        </tbody>
    </table>

    <div class="totals">
        <table>
            <tr>
                <td class="label">Subtotal:</td>
                <td class="amount">₹{{ number_format($invoice->subtotal, 2) }}</td>
            </tr>
            @if($invoice->tax > 0)
            <tr>
                <td class="label">Tax:</td>
                <td class="amount">₹{{ number_format($invoice->tax, 2) }}</td>
            </tr>
            @endif
            @if($invoice->discount > 0)
            <tr>
                <td class="label">Discount:</td>
                <td class="amount">-₹{{ number_format($invoice->discount, 2) }}</td>
            </tr>
            @endif
            <tr class="total">
                <td class="label">Total:</td>
                <td class="amount">₹{{ number_format($invoice->total, 2) }}</td>
            </tr>
            <tr>
                <td class="label">Amount Paid:</td>
                <td class="amount">₹{{ number_format($invoice->amount_paid, 2) }}</td>
            </tr>
            <tr class="total">
                <td class="label">Balance Due:</td>
                <td class="amount">₹{{ number_format($invoice->balance_due, 2) }}</td>
            </tr>
        </table>
    </div>

    @if($invoice->payments->count() > 0)
    <h3 style="margin-top: 30px;">Payment History</h3>
    <table class="items-table">
        <thead>
            <tr>
                <th>Date</th>
                <th>Method</th>
                <th>Transaction ID</th>
                <th class="amount">Amount</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach($invoice->payments as $payment)
            <tr>
                <td>{{ $payment->paid_at?->format('d M Y H:i') ?? '—' }}</td>
                <td>{{ ucfirst(str_replace('_', ' ', $payment->method ?? 'unknown')) }}</td>
                <td>{{ $payment->razorpay_payment_id ?? $payment->gateway_txn_id ?? '—' }}</td>
                <td class="amount">₹{{ number_format($payment->amount, 2) }}</td>
                <td>
                    <span class="status status-{{ $payment->status === 'success' ? 'paid' : 'unpaid' }}">
                        {{ ucfirst($payment->status ?? 'unknown') }}
                    </span>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @endif

    @if($invoice->notes)
    <div style="margin-top: 30px;">
        <h3>Notes</h3>
        <p>{{ $invoice->notes }}</p>
    </div>
    @endif

    <div class="footer">
        <p>Thank you for your business!</p>
        <p>Generated on {{ now()->format('d M Y H:i') }}</p>
    </div>
</body>
</html>