<?php

use App\Http\Controllers\PaymentController;
use App\Http\Controllers\RazorpayAccountController;
use App\Http\Controllers\SubscriptionController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// Razorpay webhooks (no auth required)
Route::post('/razorpay/webhook', [SubscriptionController::class, 'webhookHandler']);

// Protected routes
Route::middleware('auth:sanctum')->group(function () {
    // Subscription routes
    Route::post('/subscription/create', [SubscriptionController::class, 'createSubscription']);

    // Payment routes
    Route::post('/payment/create-order', [PaymentController::class, 'createOrder']);
    Route::post('/payment/verify', [PaymentController::class, 'verifyPayment']);
    Route::post('/payment/transfer', [PaymentController::class, 'transferToGym']);

    // Razorpay account routes
    Route::post('/razorpay/account/create', [RazorpayAccountController::class, 'createLinkedAccount']);
    Route::get('/razorpay/account/status', [RazorpayAccountController::class, 'getAccountStatus']);
});