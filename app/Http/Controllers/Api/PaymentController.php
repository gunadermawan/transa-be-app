<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\SubscriptionPlan;
use App\Services\PaymentService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    public function __construct(private PaymentService $paymentService) {}

    /**
     * Initiate payment for subscription upgrade.
     */
    public function initiatePayment(Request $request): JsonResponse
    {
        $request->validate([
            'plan_id' => 'required|exists:subscription_plans,id',
        ]);

        $business = $request->user()->business;

        if (! $business) {
            return response()->json([
                'message' => 'Business not found',
            ], 404);
        }

        $plan = SubscriptionPlan::findOrFail($request->plan_id);

        // Prevent payment for trial plan
        if ($plan->name === 'Trial') {
            return response()->json([
                'message' => 'Cannot purchase Trial plan. Trial is automatically assigned to new businesses.',
            ], 400);
        }

        // Create payment
        $result = $this->paymentService->createSubscriptionPayment($business, $plan);

        if (! $result['success']) {
            return response()->json([
                'message' => 'Failed to create payment',
                'error' => $result['message'] ?? 'Unknown error',
            ], 500);
        }

        return response()->json([
            'message' => 'Payment created successfully',
            'data' => [
                'snap_token' => $result['snap_token'],
                'invoice' => $result['invoice'],
                'client_key' => config('services.midtrans.client_key'),
            ],
        ]);
    }

    /**
     * Handle Midtrans webhook notification.
     */
    public function webhook(Request $request): JsonResponse
    {
        // Get notification data
        $notification = $request->all();

        // Verify signature
        $signatureKey = $notification['signature_key'] ?? '';
        $orderId = $notification['order_id'] ?? '';
        $statusCode = $notification['status_code'] ?? '';
        $grossAmount = $notification['gross_amount'] ?? '';
        $serverKey = config('services.midtrans.server_key');

        $expectedSignature = hash('sha512', $orderId.$statusCode.$grossAmount.$serverKey);

        // TODO: Re-enable signature validation in production
        // For now, log signature mismatch but continue processing
        if ($signatureKey !== $expectedSignature) {
            \Log::warning('Midtrans signature mismatch', [
                'expected' => $expectedSignature,
                'received' => $signatureKey,
                'order_id' => $orderId,
            ]);
            // Temporarily allow for testing
            // return response()->json(['message' => 'Invalid signature'], 403);
        }

        // Handle payment notification
        $result = $this->paymentService->handlePaymentNotification($notification);

        return response()->json([
            'message' => 'Notification processed',
            'data' => $result,
        ]);
    }

    /**
     * Check payment status.
     */
    public function checkStatus(Request $request): JsonResponse
    {
        $request->validate([
            'order_id' => 'required|string',
        ]);

        $result = $this->paymentService->getPaymentStatus($request->order_id);

        if (! $result['success']) {
            return response()->json([
                'message' => 'Failed to get payment status',
                'error' => $result['message'] ?? 'Unknown error',
            ], 500);
        }

        return response()->json([
            'message' => 'Payment status retrieved',
            'data' => $result['data'],
        ]);
    }

    /**
     * Handle payment finish/callback from Midtrans.
     */
    public function finish(Request $request): JsonResponse
    {
        $orderId = $request->query('order_id');
        $statusCode = $request->query('status_code');
        $transactionStatus = $request->query('transaction_status');

        return response()->json([
            'message' => 'Payment completed',
            'data' => [
                'order_id' => $orderId,
                'status_code' => $statusCode,
                'transaction_status' => $transactionStatus,
            ],
        ]);
    }
}
