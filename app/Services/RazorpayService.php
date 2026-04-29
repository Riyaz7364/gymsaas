<?php

namespace App\Services;

use Razorpay\Api\Api;
use Illuminate\Support\Facades\Log;

class RazorpayService
{
    protected Api $api;

    public function __construct()
    {
        $this->api = new Api(
            config('services.razorpay.key_id'),
            config('services.razorpay.key_secret')
        );
    }

    /**
     * Create a Razorpay subscription for gym owner
     */
    public function createSubscription(array $data): array
    {
        try {
            $subscription = $this->api->subscription->create([
                'plan_id' => $data['plan_id'],
                'customer_id' => $data['customer_id'],
                'total_count' => $data['total_count'] ?? 12, // 12 months default
                'start_at' => $data['start_at'] ?? time(),
                'addons' => $data['addons'] ?? [],
                'notes' => $data['notes'] ?? [],
            ]);

            return $subscription->toArray();
        } catch (\Exception $e) {
            Log::error('Razorpay subscription creation failed', [
                'error' => $e->getMessage(),
                'data' => $data
            ]);
            throw $e;
        }
    }

    /**
     * Create a Razorpay order for member payment
     */
    public function createOrder(array $data): array
    {
        try {
            $order = $this->api->order->create([
                'receipt' => $data['receipt'],
                'amount' => $data['amount'], // Amount in paisa
                'currency' => $data['currency'] ?? 'INR',
                'notes' => $data['notes'] ?? [],
            ]);

            return $order->toArray();
        } catch (\Exception $e) {
            Log::error('Razorpay order creation failed', [
                'error' => $e->getMessage(),
                'data' => $data
            ]);
            throw $e;
        }
    }

    /**
     * Verify payment signature
     */
    public function verifyPaymentSignature(array $attributes): bool
    {
        try {
            $expectedSignature = hash_hmac(
                'sha256',
                $attributes['razorpay_order_id'] . '|' . $attributes['razorpay_payment_id'],
                config('services.razorpay.key_secret')
            );

            return hash_equals($expectedSignature, $attributes['razorpay_signature']);
        } catch (\Exception $e) {
            Log::error('Payment signature verification failed', [
                'error' => $e->getMessage(),
                'attributes' => $attributes
            ]);
            return false;
        }
    }

    /**
     * Verify webhook signature
     */
    public function verifyWebhookSignature(string $payload, string $signature): bool
    {
        try {
            $expectedSignature = hash_hmac(
                'sha256',
                $payload,
                config('services.razorpay.webhook_secret')
            );

            return hash_equals($expectedSignature, $signature);
        } catch (\Exception $e) {
            Log::error('Webhook signature verification failed', [
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    /**
     * Create linked account for gym owner
     */
    public function createLinkedAccount(array $data): array
    {
        try {
            $account = $this->api->account->create([
                'email' => $data['email'],
                'phone' => $data['phone'],
                'type' => 'route',
                'reference_id' => $data['reference_id'],
                'legal_business_name' => $data['legal_business_name'],
                'business_type' => $data['business_type'] ?? 'partnership',
                'contact_name' => $data['contact_name'],
                'profile' => [
                    'category' => $data['category'] ?? 'health_fitness',
                    'subcategory' => $data['subcategory'] ?? 'gym',
                    'addresses' => [
                        'registered' => [
                            'street1' => $data['street1'],
                            'street2' => $data['street2'] ?? null,
                            'city' => $data['city'],
                            'state' => $data['state'],
                            'postal_code' => $data['postal_code'],
                            'country' => $data['country'] ?? 'IN',
                        ],
                    ],
                ],
            ]);

            return $account->toArray();
        } catch (\Exception $e) {
            Log::error('Razorpay linked account creation failed', [
                'error' => $e->getMessage(),
                'data' => $data
            ]);
            throw $e;
        }
    }

    /**
     * Transfer payment to gym owner's linked account
     */
    public function transferToAccount(string $paymentId, string $accountId, int $amount): array
    {
        try {
            $transfer = $this->api->payment->fetch($paymentId)->transfer([
                'transfers' => [
                    [
                        'account' => $accountId,
                        'amount' => $amount,
                        'currency' => 'INR',
                        'notes' => [
                            'transfer_type' => 'gym_payment',
                        ],
                    ],
                ],
            ]);

            return $transfer->toArray();
        } catch (\Exception $e) {
            Log::error('Razorpay transfer failed', [
                'error' => $e->getMessage(),
                'payment_id' => $paymentId,
                'account_id' => $accountId,
                'amount' => $amount
            ]);
            throw $e;
        }
    }

    /**
     * Fetch subscription details
     */
    public function getSubscription(string $subscriptionId): array
    {
        try {
            $subscription = $this->api->subscription->fetch($subscriptionId);
            return $subscription->toArray();
        } catch (\Exception $e) {
            Log::error('Failed to fetch subscription', [
                'error' => $e->getMessage(),
                'subscription_id' => $subscriptionId
            ]);
            throw $e;
        }
    }

    /**
     * Fetch payment details
     */
    public function getPayment(string $paymentId): array
    {
        try {
            $payment = $this->api->payment->fetch($paymentId);
            return $payment->toArray();
        } catch (\Exception $e) {
            Log::error('Failed to fetch payment', [
                'error' => $e->getMessage(),
                'payment_id' => $paymentId
            ]);
            throw $e;
        }
    }

    /**
     * Create customer for subscription
     */
    public function createCustomer(array $data): array
    {
        try {
            $customer = $this->api->customer->create([
                'name' => $data['name'],
                'email' => $data['email'],
                'contact' => $data['contact'],
                'notes' => $data['notes'] ?? [],
            ]);

            return $customer->toArray();
        } catch (\Exception $e) {
            Log::error('Razorpay customer creation failed', [
                'error' => $e->getMessage(),
                'data' => $data
            ]);
            throw $e;
        }
    }
}