<?php

namespace App\Http\Middleware;

use App\Services\SubscriptionService;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckSubscriptionLimit
{
    public function __construct(private SubscriptionService $subscriptionService) {}

    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (! $user || ! $user->business_id) {
            return response()->json([
                'message' => 'Business not found',
            ], 403);
        }

        $business = $user->business;

        if (! $business) {
            return response()->json([
                'message' => 'Business not found',
            ], 403);
        }

        // Check if subscription is active
        if (! $this->subscriptionService->hasActiveSubscription($business)) {
            return response()->json([
                'message' => 'Your subscription has expired. Please renew to continue.',
                'code' => 'SUBSCRIPTION_EXPIRED',
            ], 403);
        }

        return $next($request);
    }
}
