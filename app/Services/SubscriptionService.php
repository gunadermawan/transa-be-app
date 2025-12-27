<?php

namespace App\Services;

use App\Models\Business;
use App\Models\BusinessSubscription;
use App\Models\Order;
use App\Models\Outlet;
use App\Models\Product;
use App\Models\SubscriptionPlan;
use App\Models\User;

class SubscriptionService
{
    /**
     * Assign trial subscription to a new business.
     */
    public function assignTrialSubscription(Business $business): BusinessSubscription
    {
        $trialPlan = SubscriptionPlan::where('name', 'Trial')->firstOrFail();

        $subscription = BusinessSubscription::create([
            'business_id' => $business->id,
            'subscription_plan_id' => $trialPlan->id,
            'start_date' => now(),
            'end_date' => now()->addDays($trialPlan->trial_days),
            'trial_ends_at' => now()->addDays($trialPlan->trial_days),
            'next_billing_date' => null,
            'status' => 'trial',
            'auto_renew' => false,
        ]);

        $business->update([
            'current_subscription_id' => $subscription->id,
            'subscription_status' => 'trial',
            'activated_at' => now(),
            'expired_at' => now()->addDays($trialPlan->trial_days),
        ]);

        return $subscription;
    }

    /**
     * Get active subscription for a business.
     */
    public function getActiveSubscription(Business $business): ?BusinessSubscription
    {
        return $business->currentSubscription()->with('plan')->first();
    }

    /**
     * Check if business has active subscription.
     */
    public function hasActiveSubscription(Business $business): bool
    {
        $subscription = $this->getActiveSubscription($business);

        if (! $subscription) {
            return false;
        }

        if (in_array($subscription->status, ['cancelled', 'expired'])) {
            return false;
        }

        if ($subscription->end_date && $subscription->end_date->isPast()) {
            return false;
        }

        return true;
    }

    /**
     * Check if subscription is expired.
     */
    public function isExpired(Business $business): bool
    {
        return ! $this->hasActiveSubscription($business);
    }

    /**
     * Get current usage for a business.
     */
    public function getCurrentUsage(Business $business): array
    {
        return [
            'outlets' => Outlet::where('business_id', $business->id)->count(),
            'users' => User::where('business_id', $business->id)->count(),
            'products' => Product::where('business_id', $business->id)->count(),
            'transactions_this_month' => Order::whereHas('outlet', function ($query) use ($business) {
                $query->where('business_id', $business->id);
            })
                ->whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year)
                ->count(),
        ];
    }

    /**
     * Check if business can add more outlets.
     */
    public function canAddOutlet(Business $business): bool
    {
        $subscription = $this->getActiveSubscription($business);

        if (! $subscription || ! $this->hasActiveSubscription($business)) {
            return false;
        }

        $plan = $subscription->plan;

        if ($plan->max_outlets === null) {
            return true;
        }

        $currentOutlets = Outlet::where('business_id', $business->id)->count();

        return $currentOutlets < $plan->max_outlets;
    }

    /**
     * Check if business can add more users.
     */
    public function canAddUser(Business $business): bool
    {
        $subscription = $this->getActiveSubscription($business);

        if (! $subscription || ! $this->hasActiveSubscription($business)) {
            return false;
        }

        $plan = $subscription->plan;

        if ($plan->max_users === null) {
            return true;
        }

        $currentUsers = User::where('business_id', $business->id)->count();

        return $currentUsers < $plan->max_users;
    }

    /**
     * Check if business can add more products.
     */
    public function canAddProduct(Business $business): bool
    {
        $subscription = $this->getActiveSubscription($business);

        if (! $subscription || ! $this->hasActiveSubscription($business)) {
            return false;
        }

        $plan = $subscription->plan;

        if ($plan->max_products === null) {
            return true;
        }

        $currentProducts = Product::where('business_id', $business->id)->count();

        return $currentProducts < $plan->max_products;
    }

    /**
     * Check if business can add more transactions this month.
     */
    public function canAddTransaction(Business $business): bool
    {
        $subscription = $this->getActiveSubscription($business);

        if (! $subscription || ! $this->hasActiveSubscription($business)) {
            return false;
        }

        $plan = $subscription->plan;

        if ($plan->max_transactions_per_month === null) {
            return true;
        }

        $currentTransactions = Order::whereHas('outlet', function ($query) use ($business) {
            $query->where('business_id', $business->id);
        })
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->count();

        return $currentTransactions < $plan->max_transactions_per_month;
    }

    /**
     * Upgrade or downgrade subscription.
     */
    public function changeSubscription(Business $business, SubscriptionPlan $newPlan): BusinessSubscription
    {
        $currentSubscription = $this->getActiveSubscription($business);

        if ($currentSubscription) {
            $currentSubscription->update([
                'status' => 'cancelled',
                'cancelled_at' => now(),
                'cancellation_reason' => 'Upgraded to '.$newPlan->name,
            ]);
        }

        $subscription = BusinessSubscription::create([
            'business_id' => $business->id,
            'subscription_plan_id' => $newPlan->id,
            'start_date' => now(),
            'end_date' => now()->addMonth(),
            'trial_ends_at' => null,
            'next_billing_date' => now()->addMonth(),
            'status' => 'active',
            'auto_renew' => true,
        ]);

        $business->update([
            'current_subscription_id' => $subscription->id,
            'subscription_status' => 'active',
            'activated_at' => now(),
            'expired_at' => now()->addMonth(),
        ]);

        return $subscription;
    }

    /**
     * Get subscription with limits and usage.
     */
    public function getSubscriptionDetails(Business $business): array
    {
        $subscription = $this->getActiveSubscription($business);

        if (! $subscription) {
            return [
                'subscription' => null,
                'plan' => null,
                'usage' => $this->getCurrentUsage($business),
                'limits' => [],
                'is_active' => false,
                'days_remaining' => 0,
            ];
        }

        $plan = $subscription->plan;
        $usage = $this->getCurrentUsage($business);

        return [
            'subscription' => $subscription,
            'plan' => $plan,
            'usage' => $usage,
            'limits' => [
                'outlets' => $plan->max_outlets,
                'users' => $plan->max_users,
                'products' => $plan->max_products,
                'transactions_per_month' => $plan->max_transactions_per_month,
            ],
            'is_active' => $this->hasActiveSubscription($business),
            'days_remaining' => $subscription->end_date ? now()->diffInDays($subscription->end_date, false) : null,
        ];
    }
}
