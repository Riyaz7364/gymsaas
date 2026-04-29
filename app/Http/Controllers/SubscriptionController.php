<?php

namespace App\Http\Controllers;

use App\Events\PaymentCaptured;
use App\Jobs\TransferPaymentJob;
use App\Models\Gym;
use App\Models\GymSubscription;
use App\Models\Payment;
use App\Services\RazorpayService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;

class SubscriptionController extends Controller
{
    public function __construct(
        protected RazorpayService $razorpayService
    ) {}

    /**
     * Create subscription for gym owner
     */
    public function createSubscription(Request $request): JsonResponse
    {
        $request->validate([
            'plan_id' => 'required|string',
            'total_count' => 'nullable|integer|min:1|max:12',
        ]);

        $user = auth()->user();
        $gym = $user->gym;

        if (!$gym) {
            return response()->json(['error' => 'No gym associated with user'], 400);
        }

        try {
            // Create or get Razorpay customer
            $customerData = [
                'name' => $user->name,
                'email' => $user->email,
                'contact' => $gym->phone,
                'notes' => [
                    'gym_id' => $gym->id,
                    'user_id' => $user->id,
                ],
            ];

            $customer = $this->razorpayService->createCustomer($customerData);

            // Create subscription
            $subscriptionData = [
                'plan_id' => $request->plan_id,
                'customer_id' => $customer['id'],
                'total_count' => $request->total_count ?? 12,
                'notes' => [
                    'gym_id' => $gym->id,
                    'user_id' => $user->id,
                ],
            ];

            $subscription = $this->razorpayService->createSubscription($subscriptionData);

            // Save subscription in database
            GymSubscription::create([
                'gym_id' => $gym->id,
                'razorpay_subscription_id' => $subscription['id'],
                'plan_id' => $request->plan_id,
                'status' => 'active',
                'expires_at' => now()->addMonths($request->total_count ?? 12),
                'subscription_data' => $subscription,
            ]);

            return response()->json([
                'subscription' => $subscription,
                'checkout_url' => $subscription['short_url'],
            ]);

        } catch (\Exception $e) {
            Log::error('Subscription creation failed', [
                'error' => $e->getMessage(),
                'user_id' => $user->id,
                'gym_id' => $gym->id,
            ]);

            return response()->json(['error' => 'Failed to create subscription'], 500);
        }
    }

    /**
     * Handle Razorpay webhooks
     */
    public function webhookHandler(Request $request): JsonResponse
    {
        $payload = $request->getContent();
        $signature = $request->header('X-Razorpay-Signature');

        // Verify webhook signature
        if (!$this->razorpayService->verifyWebhookSignature($payload, $signature)) {
            Log::warning('Invalid webhook signature', ['payload' => $payload]);
            return response()->json(['error' => 'Invalid signature'], 400);
        }

        $event = json_decode($payload, true);

        Log::info('Razorpay webhook received', [
            'event' => $event['event'],
            'entity_id' => $event['data']['entity']['id'] ?? null,
        ]);

        try {
            switch ($event['event']) {
                case 'subscription.charged':
                    $this->handleSubscriptionCharged($event['data']['entity']);
                    break;

                case 'payment.captured':
                    $this->handlePaymentCaptured($event['data']['entity']);
                    break;

                case 'payment.failed':
                    $this->handlePaymentFailed($event['data']['entity']);
                    break;

                default:
                    Log::info('Unhandled webhook event', ['event' => $event['event']]);
            }

            return response()->json(['status' => 'ok']);

        } catch (\Exception $e) {
            Log::error('Webhook processing failed', [
                'error' => $e->getMessage(),
                'event' => $event['event'],
            ]);

            return response()->json(['error' => 'Processing failed'], 500);
        }
    }

    /**
     * Handle subscription charged event
     */
    protected function handleSubscriptionCharged(array $subscription): void
    {
        $gymSubscription = GymSubscription::where('razorpay_subscription_id', $subscription['id'])->first();

        if ($gymSubscription) {
            $gymSubscription->update([
                'status' => 'active',
                'expires_at' => now()->addMonths(1), // Extend by 1 month
                'subscription_data' => $subscription,
            ]);

            Log::info('Gym subscription activated', [
                'gym_id' => $gymSubscription->gym_id,
                'subscription_id' => $subscription['id'],
            ]);
        }
    }

    /**
     * Handle payment captured event
     */
    protected function handlePaymentCaptured(array $payment): void
    {
        $dbPayment = Payment::where('razorpay_payment_id', $payment['id'])->first();

        if ($dbPayment) {
            $dbPayment->update([
                'status' => 'success',
                'paid_at' => now(),
            ]);

            // Dispatch transfer job
            TransferPaymentJob::dispatch($dbPayment);

            // Fire event
            PaymentCaptured::dispatch($dbPayment);

            Log::info('Payment captured and transfer queued', [
                'payment_id' => $payment['id'],
                'amount' => $payment['amount'],
            ]);
        }
    }

    /**
     * Handle payment failed event
     */
    protected function handlePaymentFailed(array $payment): void
    {
        $dbPayment = Payment::where('razorpay_payment_id', $payment['id'])->first();

        if ($dbPayment) {
            $dbPayment->update([
                'status' => 'failed',
            ]);

            Log::warning('Payment failed', [
                'payment_id' => $payment['id'],
                'amount' => $payment['amount'],
            ]);
        }
    }
}
