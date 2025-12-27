<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\SubscriptionPlan;
use App\Services\SubscriptionService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SubscriptionController extends Controller
{
    public function __construct(private SubscriptionService $subscriptionService) {}

    /**
     * Get all available subscription plans.
     */
    public function getPlans(): JsonResponse
    {
        $plans = SubscriptionPlan::where('status', 'active')
            ->orderBy('sort_order')
            ->get()
            ->map(function ($plan) {
                return [
                    'id' => $plan->id,
                    'name' => $plan->name,
                    'description' => $plan->description,
                    'price' => $plan->price,
                    'billing_cycle' => $plan->billing_cycle,
                    'trial_days' => $plan->trial_days,
                    'limits' => [
                        'max_outlets' => $plan->max_outlets,
                        'max_users' => $plan->max_users,
                        'max_products' => $plan->max_products,
                        'max_transactions_per_month' => $plan->max_transactions_per_month,
                    ],
                    'features' => json_decode($plan->features),
                    'is_popular' => $plan->is_popular,
                ];
            });

        return response()->json([
            'data' => $plans,
        ]);
    }

    /**
     * Get current subscription details.
     */
    public function getCurrentSubscription(Request $request): JsonResponse
    {
        $business = $request->user()->business;

        if (! $business) {
            return response()->json([
                'message' => 'Business not found',
            ], 404);
        }

        $details = $this->subscriptionService->getSubscriptionDetails($business);

        return response()->json([
            'data' => $details,
        ]);
    }

    /**
     * Get current usage statistics.
     */
    public function getUsage(Request $request): JsonResponse
    {
        $business = $request->user()->business;

        if (! $business) {
            return response()->json([
                'message' => 'Business not found',
            ], 404);
        }

        $usage = $this->subscriptionService->getCurrentUsage($business);
        $subscription = $this->subscriptionService->getActiveSubscription($business);

        $limits = [
            'max_outlets' => $subscription?->plan->max_outlets,
            'max_users' => $subscription?->plan->max_users,
            'max_products' => $subscription?->plan->max_products,
            'max_transactions_per_month' => $subscription?->plan->max_transactions_per_month,
        ];

        return response()->json([
            'data' => [
                'usage' => $usage,
                'limits' => $limits,
                'can_add' => [
                    'outlet' => $this->subscriptionService->canAddOutlet($business),
                    'user' => $this->subscriptionService->canAddUser($business),
                    'product' => $this->subscriptionService->canAddProduct($business),
                    'transaction' => $this->subscriptionService->canAddTransaction($business),
                ],
            ],
        ]);
    }

    /**
     * Upgrade or change subscription plan.
     */
    public function changePlan(Request $request): JsonResponse
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

        // Prevent downgrading to trial
        if ($plan->name === 'Trial') {
            return response()->json([
                'message' => 'Cannot change to Trial plan. Trial is only for new businesses.',
            ], 400);
        }

        $subscription = $this->subscriptionService->changeSubscription($business, $plan);

        return response()->json([
            'message' => 'Subscription plan changed successfully',
            'data' => $subscription->load('plan'),
        ]);
    }

    /**
     * Check if business can perform specific action.
     */
    public function checkLimit(Request $request): JsonResponse
    {
        $request->validate([
            'type' => 'required|in:outlet,user,product,transaction',
        ]);

        $business = $request->user()->business;

        if (! $business) {
            return response()->json([
                'message' => 'Business not found',
            ], 404);
        }

        $type = $request->type;
        $canAdd = false;

        switch ($type) {
            case 'outlet':
                $canAdd = $this->subscriptionService->canAddOutlet($business);
                break;
            case 'user':
                $canAdd = $this->subscriptionService->canAddUser($business);
                break;
            case 'product':
                $canAdd = $this->subscriptionService->canAddProduct($business);
                break;
            case 'transaction':
                $canAdd = $this->subscriptionService->canAddTransaction($business);
                break;
        }

        return response()->json([
            'data' => [
                'type' => $type,
                'can_add' => $canAdd,
            ],
        ]);
    }
}
