<?php

namespace App\Http\Controllers;

use App\Models\RazorpayAccount;
use App\Services\RazorpayService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;

class RazorpayAccountController extends Controller
{
    public function __construct(
        protected RazorpayService $razorpayService
    ) {}

    /**
     * Create linked account for gym owner
     */
    public function createLinkedAccount(Request $request): JsonResponse
    {
        $request->validate([
            'email' => 'required|email',
            'phone' => 'required|string|regex:/^[6-9]\d{9}$/',
            'legal_business_name' => 'required|string|max:255',
            'business_type' => 'nullable|string|in:proprietorship,partnership,private_limited,public_limited,llp,ngo,trust',
            'contact_name' => 'required|string|max:255',
            'street1' => 'required|string|max:255',
            'street2' => 'nullable|string|max:255',
            'city' => 'required|string|max:255',
            'state' => 'required|string|max:255',
            'postal_code' => 'required|string|regex:/^\d{6}$/',
            'country' => 'nullable|string|size:2',
            'category' => 'nullable|string',
            'subcategory' => 'nullable|string',
        ]);

        $user = auth()->user();
        $gym = $user->gym;

        if (!$gym) {
            return response()->json(['error' => 'No gym associated with user'], 400);
        }

        // Check if account already exists
        if ($gym->razorpayAccount) {
            return response()->json(['error' => 'Razorpay account already exists'], 400);
        }

        try {
            $accountData = [
                'email' => $request->email,
                'phone' => $request->phone,
                'reference_id' => 'gym_' . $gym->id,
                'legal_business_name' => $request->legal_business_name,
                'business_type' => $request->business_type ?? 'partnership',
                'contact_name' => $request->contact_name,
                'street1' => $request->street1,
                'street2' => $request->street2,
                'city' => $request->city,
                'state' => $request->state,
                'postal_code' => $request->postal_code,
                'country' => $request->country ?? 'IN',
                'category' => $request->category ?? 'health_fitness',
                'subcategory' => $request->subcategory ?? 'gym',
            ];

            $account = $this->razorpayService->createLinkedAccount($accountData);

            // Save account in database
            RazorpayAccount::create([
                'gym_id' => $gym->id,
                'razorpay_account_id' => $account['id'],
                'status' => 'created',
                'kyc_status' => 'pending',
                'account_data' => $account,
            ]);

            Log::info('Razorpay linked account created', [
                'gym_id' => $gym->id,
                'account_id' => $account['id'],
            ]);

            return response()->json([
                'account' => $account,
                'message' => 'Linked account created successfully. Please complete KYC.',
            ]);

        } catch (\Exception $e) {
            Log::error('Linked account creation failed', [
                'error' => $e->getMessage(),
                'gym_id' => $gym->id,
            ]);

            return response()->json(['error' => 'Failed to create linked account'], 500);
        }
    }

    /**
     * Get account status
     */
    public function getAccountStatus(): JsonResponse
    {
        $gym = auth()->user()->gym;

        if (!$gym) {
            return response()->json(['error' => 'No gym associated with user'], 400);
        }

        $account = $gym->razorpayAccount;

        if (!$account) {
            return response()->json(['status' => 'not_created']);
        }

        return response()->json([
            'status' => $account->status,
            'kyc_status' => $account->kyc_status,
            'account_id' => $account->razorpay_account_id,
        ]);
    }
}
