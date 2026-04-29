<?php

namespace App\Http\Controllers\Finance;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use Illuminate\Http\Request;

class PaymentHistoryController extends Controller
{
    public function index(Request $request)
    {
        $gymId = auth()->user()->gym_id;

        $query = Payment::where('gym_id', $gymId)
            ->with(['member', 'invoice'])
            ->latest();

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by method
        if ($request->filled('method')) {
            $query->where('method', $request->method);
        }

        // Filter by date range
        if ($request->filled('from_date')) {
            $query->whereDate('paid_at', '>=', $request->from_date);
        }

        if ($request->filled('to_date')) {
            $query->whereDate('paid_at', '<=', $request->to_date);
        }

        // Filter by member
        if ($request->filled('member_id')) {
            $query->where('member_id', $request->member_id);
        }

        $payments = $query->paginate(20)->withQueryString();

        // Get filter options
        $members = \App\Models\Member::where('gym_id', $gymId)->orderBy('name')->get();
        $statuses = ['pending', 'success', 'failed', 'cancelled'];
        $methods = ['cash', 'card', 'online', 'bank_transfer', 'razorpay'];

        return view('payments.index', compact('payments', 'members', 'statuses', 'methods'));
    }

    public function show($id)
    {
        $gymId = auth()->user()->gym_id;
        $payment = Payment::where('gym_id', $gymId)
            ->with(['member', 'invoice'])
            ->findOrFail($id);

        return view('payments.show', compact('payment'));
    }

    public function retryTransfer($id)
    {
        $gymId = auth()->user()->gym_id;
        $payment = Payment::where('gym_id', $gymId)->findOrFail($id);

        // Check if payment is successful and transfer is not completed
        if ($payment->status !== 'success' || $payment->transfer_status === 'completed') {
            return redirect()->back()->with('error', 'Transfer cannot be retried for this payment.');
        }

        try {
            // Use the existing transfer logic from PaymentController
            $paymentController = app(\App\Http\Controllers\PaymentController::class);
            $transferRequest = request()->merge(['payment_id' => $id]);
            $result = $paymentController->transferToGym($transferRequest);

            if ($result->getStatusCode() === 200) {
                return redirect()->back()->with('success', 'Transfer retry initiated successfully.');
            } else {
                return redirect()->back()->with('error', 'Transfer retry failed.');
            }
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Transfer retry failed: ' . $e->getMessage());
        }
    }
}
