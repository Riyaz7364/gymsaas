<?php

namespace App\Jobs;

use App\Models\Payment;
use App\Services\RazorpayService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;

class TransferPaymentJob implements ShouldQueue
{
    use Queueable;

    public int $tries = 3;
    public int $backoff = 60; // 1 minute

    protected Payment $payment;

    /**
     * Create a new job instance.
     */
    public function __construct(Payment $payment)
    {
        $this->payment = $payment;
    }

    /**
     * Execute the job.
     */
    public function handle(RazorpayService $razorpayService): void
    {
        try {
            // Check if gym has active subscription
            $gym = $this->payment->gym;
            if (!$gym->activeSubscription) {
                Log::warning('Transfer skipped: Gym subscription inactive', [
                    'payment_id' => $this->payment->id,
                    'gym_id' => $gym->id
                ]);
                return;
            }

            // Check if gym has linked account
            $razorpayAccount = $gym->razorpayAccount;
            if (!$razorpayAccount || !$razorpayAccount->isActive()) {
                Log::warning('Transfer skipped: No active Razorpay account', [
                    'payment_id' => $this->payment->id,
                    'gym_id' => $gym->id
                ]);
                return;
            }

            // Transfer full amount to gym owner
            $transfer = $razorpayService->transferToAccount(
                $this->payment->razorpay_payment_id,
                $razorpayAccount->razorpay_account_id,
                $this->payment->amount * 100 // Convert to paisa
            );

            // Update payment with transfer details
            $this->payment->update([
                'transfer_id' => $transfer['id'],
                'transfer_status' => 'completed',
            ]);

            Log::info('Payment transfer completed', [
                'payment_id' => $this->payment->id,
                'transfer_id' => $transfer['id'],
                'amount' => $this->payment->amount
            ]);

        } catch (\Exception $e) {
            Log::error('Payment transfer failed', [
                'payment_id' => $this->payment->id,
                'error' => $e->getMessage()
            ]);

            $this->payment->update([
                'transfer_status' => 'failed',
            ]);

            throw $e; // Re-throw to mark job as failed
        }
    }
}
