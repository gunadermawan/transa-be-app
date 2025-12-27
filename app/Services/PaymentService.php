<?php

namespace App\Services;

use App\Models\Business;
use App\Models\SubscriptionInvoice;
use App\Models\SubscriptionPayment;
use App\Models\SubscriptionPlan;
use Midtrans\Config;
use Midtrans\Snap;

class PaymentService
{
    public function __construct()
    {
        // Set Midtrans configuration
        Config::$serverKey = config('services.midtrans.server_key');
        Config::$isProduction = config('services.midtrans.is_production');
        Config::$isSanitized = config('services.midtrans.is_sanitized');
        Config::$is3ds = config('services.midtrans.is_3ds');
    }

    /**
     * Create payment transaction for subscription upgrade.
     */
    public function createSubscriptionPayment(Business $business, SubscriptionPlan $plan): array
    {
        // Get current subscription
        $currentSubscription = $business->currentSubscription;

        // Create invoice
        $invoice = SubscriptionInvoice::create([
            'business_id' => $business->id,
            'business_subscription_id' => $currentSubscription ? $currentSubscription->id : 1,
            'invoice_number' => $this->generateInvoiceNumber(),
            'subtotal' => $plan->price,
            'tax' => 0,
            'total' => $plan->price,
            'status' => 'pending',
            'issue_date' => now(),
            'due_date' => now()->addDays(1),
        ]);

        // Prepare transaction details for Midtrans
        $transactionDetails = [
            'order_id' => $invoice->invoice_number,
            'gross_amount' => (int) $plan->price,
        ];

        // Item details
        $itemDetails = [
            [
                'id' => 'subscription_'.$plan->id,
                'price' => (int) $plan->price,
                'quantity' => 1,
                'name' => $plan->name.' Subscription',
            ],
        ];

        // Customer details
        $customerDetails = [
            'first_name' => $business->user->name ?? 'Customer',
            'email' => $business->user->email ?? 'customer@example.com',
            'phone' => $business->phone ?? '08123456789',
        ];

        // Transaction data
        $transactionData = [
            'transaction_details' => $transactionDetails,
            'item_details' => $itemDetails,
            'customer_details' => $customerDetails,
        ];

        try {
            // Get Snap token from Midtrans
            $snapToken = Snap::getSnapToken($transactionData);

            // Store snap token in invoice
            $invoice->update([
                'payment_url' => $snapToken,
            ]);

            return [
                'success' => true,
                'snap_token' => $snapToken,
                'invoice' => $invoice,
            ];
        } catch (\Exception $e) {
            // Update invoice status to failed
            $invoice->update(['status' => 'failed']);

            return [
                'success' => false,
                'message' => $e->getMessage(),
            ];
        }
    }

    /**
     * Handle Midtrans payment notification.
     */
    public function handlePaymentNotification(array $notification): array
    {
        \Log::info('=== WEBHOOK NOTIFICATION RECEIVED ===', [
            'notification' => $notification,
        ]);

        $orderId = $notification['order_id'];
        $transactionStatus = $notification['transaction_status'];
        $fraudStatus = $notification['fraud_status'] ?? null;

        \Log::info('Processing payment notification', [
            'order_id' => $orderId,
            'transaction_status' => $transactionStatus,
            'fraud_status' => $fraudStatus,
        ]);

        $invoice = SubscriptionInvoice::where('invoice_number', $orderId)->first();

        if (! $invoice) {
            \Log::error('Invoice not found', ['order_id' => $orderId]);

            return [
                'success' => false,
                'message' => 'Invoice not found',
            ];
        }

        \Log::info('Invoice found', [
            'invoice_id' => $invoice->id,
            'business_id' => $invoice->business_id,
            'total' => $invoice->total,
        ]);

        // Create payment record
        $paymentData = [
            'subscription_invoice_id' => $invoice->id,
            'business_id' => $invoice->business_id,
            'payment_method' => $notification['payment_type'] ?? 'unknown',
            'amount' => $notification['gross_amount'] ?? $invoice->total,
            'transaction_id' => $notification['transaction_id'] ?? null,
            'gateway' => 'midtrans',
            'status' => 'pending',
            'payment_date' => now(),
            'gateway_response' => json_encode($notification),
        ];

        \Log::info('Creating payment record', ['payment_data' => $paymentData]);

        $payment = SubscriptionPayment::create($paymentData);

        \Log::info('Payment record created successfully', [
            'payment_id' => $payment->id,
        ]);

        // Handle different transaction statuses
        \Log::info('Handling transaction status', [
            'transaction_status' => $transactionStatus,
            'fraud_status' => $fraudStatus,
        ]);

        if ($transactionStatus == 'capture') {
            if ($fraudStatus == 'accept') {
                \Log::info('Payment captured and accepted, processing successful payment');
                $this->handleSuccessfulPayment($invoice, $payment);
            }
        } elseif ($transactionStatus == 'settlement') {
            \Log::info('Payment settled, processing successful payment');
            $this->handleSuccessfulPayment($invoice, $payment);
        } elseif ($transactionStatus == 'pending') {
            \Log::info('Payment pending');
            $invoice->update(['status' => 'pending']);
            $payment->update(['status' => 'pending']);
        } elseif (in_array($transactionStatus, ['deny', 'expire', 'cancel'])) {
            \Log::warning('Payment failed or cancelled', ['status' => $transactionStatus]);
            $invoice->update(['status' => 'failed']);
            $payment->update(['status' => 'failed']);
        }

        \Log::info('=== WEBHOOK PROCESSING COMPLETED ===');

        return [
            'success' => true,
            'invoice' => $invoice,
            'payment' => $payment,
        ];
    }

    /**
     * Handle successful payment.
     */
    protected function handleSuccessfulPayment(SubscriptionInvoice $invoice, SubscriptionPayment $payment): void
    {
        \Log::info('=== HANDLING SUCCESSFUL PAYMENT ===', [
            'invoice_id' => $invoice->id,
            'payment_id' => $payment->id,
        ]);

        $invoice->update([
            'status' => 'paid',
            'paid_at' => now(),
        ]);

        \Log::info('Invoice marked as paid');

        $payment->update([
            'status' => 'success',
        ]);

        \Log::info('Payment marked as success');

        // Activate subscription via SubscriptionService
        $subscriptionService = new SubscriptionService;
        $business = $invoice->business;

        \Log::info('Activating subscription', [
            'business_id' => $business->id,
        ]);

        // Get plan from invoice amount (not from business subscription)
        // The invoice amount determines the target plan user wants to purchase
        $plan = SubscriptionPlan::where('price', $invoice->total)->first();

        \Log::info('Looking for plan by invoice amount', [
            'invoice_amount' => $invoice->total,
            'plan_id' => $plan?->id,
            'plan_name' => $plan?->name,
        ]);

        if ($plan) {
            \Log::info('Changing subscription to plan', [
                'plan_id' => $plan->id,
                'plan_name' => $plan->name,
            ]);
            $subscriptionService->changeSubscription($business, $plan);
            \Log::info('Subscription changed successfully');
        } else {
            \Log::error('No plan found, cannot activate subscription');
        }

        \Log::info('=== SUCCESSFUL PAYMENT HANDLING COMPLETED ===');
    }

    /**
     * Generate unique invoice number.
     */
    protected function generateInvoiceNumber(): string
    {
        $prefix = 'INV';
        $date = now()->format('Ymd');
        $random = strtoupper(substr(md5(uniqid(mt_rand(), true)), 0, 6));

        return "{$prefix}-{$date}-{$random}";
    }

    /**
     * Get payment status from Midtrans.
     */
    public function getPaymentStatus(string $orderId): array
    {
        try {
            $status = \Midtrans\Transaction::status($orderId);

            return [
                'success' => true,
                'data' => $status,
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => $e->getMessage(),
            ];
        }
    }
}
