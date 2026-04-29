<?php

namespace App\Http\Controllers;

use App\Events\PaymentCaptured;
use App\Jobs\TransferPaymentJob;
use App\Models\Gym;
use App\Models\Payment;
use App\Services\RazorpayService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;

class PaymentController extends Controller
{
    public function __construct(
        protected RazorpayService $razorpayService
    ) {}

    /**
     * Create Razorpay order for member payment
     */
    public function createOrder(Request $request): JsonResponse
    {
        $request->validate([
            'amount' => 'required|numeric|min:1',
            'currency' => 'nullable|string|size:3',
            'receipt' => 'required|string|max:40',
            'member_id' => 'required|exists:members,id',
        ]);

        $member = auth()->user(); // Assuming member is authenticated
        $gym = $member->gym;

        // Check if gym subscription is active
        if (!$gym->activeSubscription) {
            return response()->json(['error' => 'Gym subscription is inactive'], 403);
        }

        try {
            $orderData = [
                'receipt' => $request->receipt,
                'amount' => $request->amount * 100, // Convert to paisa
                'currency' => $request->currency ?? 'INR',
                'notes' => [
                    'member_id' => $request->member_id,
                    'gym_id' => $gym->id,
                    'user_id' => $member->id,
                ],
            ];

            $order = $this->razorpayService->createOrder($orderData);

            // Save payment record
            Payment::create([
                'gym_id' => $gym->id,
                'member_id' => $request->member_id,
                'amount' => $request->amount,
                'currency' => $request->currency ?? 'INR',
                'status' => 'pending',
                'razorpay_order_id' => $order['id'],
                'receipt' => $request->receipt,
            ]);

            return response()->json([
                'order' => $order,
                'razorpay_key' => config('services.razorpay.key_id'),
            ]);

        } catch (\Exception $e) {
            Log::error('Order creation failed', [
                'error' => $e->getMessage(),
                'member_id' => $request->member_id,
                'amount' => $request->amount,
            ]);

            return response()->json(['error' => 'Failed to create order'], 500);
        }
    }

    /**
     * Verify payment after successful checkout
     */
    public function verifyPayment(Request $request): JsonResponse
    {
        $request->validate([
            'razorpay_order_id' => 'required|string',
            'razorpay_payment_id' => 'required|string',
            'razorpay_signature' => 'required|string',
        ]);

        // Verify signature
        if (!$this->razorpayService->verifyPaymentSignature($request->all())) {
            Log::warning('Invalid payment signature', $request->all());
            return response()->json(['error' => 'Invalid payment signature'], 400);
        }

        try {
            // Update payment record
            $payment = Payment::where('razorpay_order_id', $request->razorpay_order_id)->first();

            if (!$payment) {
                return response()->json(['error' => 'Payment not found'], 404);
            }

            $payment->update([
                'razorpay_payment_id' => $request->razorpay_payment_id,
                'status' => 'success',
                'paid_at' => now(),
            ]);

            // Dispatch transfer job
            TransferPaymentJob::dispatch($payment);

            // Fire event
            PaymentCaptured::dispatch($payment);

            Log::info('Payment verified and transfer queued', [
                'payment_id' => $payment->id,
                'razorpay_payment_id' => $request->razorpay_payment_id,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Payment verified successfully',
                'payment_id' => $payment->id,
            ]);

        } catch (\Exception $e) {
            Log::error('Payment verification failed', [
                'error' => $e->getMessage(),
                'razorpay_payment_id' => $request->razorpay_payment_id,
            ]);

            return response()->json(['error' => 'Payment verification failed'], 500);
        }
    }

    /**
     * Transfer payment to gym owner (manual trigger if needed)
     */
    public function transferToGym(Request $request): JsonResponse
    {
        $request->validate([
            'payment_id' => 'required|exists:payments,id',
        ]);

        $payment = Payment::find($request->payment_id);
        $gym = $payment->gym;

        // Check permissions (only gym owner or admin)
        if (auth()->user()->gym_id !== $gym->id && !auth()->user()->isSuperAdmin()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        // Check if gym has active subscription
        if (!$gym->activeSubscription) {
            return response()->json(['error' => 'Gym subscription is inactive'], 403);
        }

        // Check if gym has linked account
        $razorpayAccount = $gym->razorpayAccount;
        if (!$razorpayAccount || !$razorpayAccount->isActive()) {
            return response()->json(['error' => 'No active Razorpay account'], 400);
        }

        try {
            // Transfer payment
            $transfer = $this->razorpayService->transferToAccount(
                $payment->razorpay_payment_id,
                $razorpayAccount->razorpay_account_id,
                $payment->amount * 100
            );

            $payment->update([
                'transfer_id' => $transfer['id'],
                'transfer_status' => 'completed',
            ]);

            return response()->json([
                'success' => true,
                'transfer_id' => $transfer['id'],
            ]);

        } catch (\Exception $e) {
            Log::error('Manual transfer failed', [
                'error' => $e->getMessage(),
                'payment_id' => $payment->id,
            ]);

            $payment->update([
                'transfer_status' => 'failed',
            ]);

            return response()->json(['error' => 'Transfer failed'], 500);
        }
    }
}
