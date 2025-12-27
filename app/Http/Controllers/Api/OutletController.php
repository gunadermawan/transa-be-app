<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Outlet;
use App\Services\SubscriptionService;
use Illuminate\Http\Request;

class OutletController extends Controller
{
    public function __construct(private SubscriptionService $subscriptionService) {}

    // add outlet to business
    public function addOutlet(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'address' => 'required|string',
        ]);

        $business = $request->user()->business;

        // Check subscription limit
        if (! $this->subscriptionService->canAddOutlet($business)) {
            $subscription = $this->subscriptionService->getActiveSubscription($business);
            $maxOutlets = $subscription?->plan->max_outlets ?? 0;

            return response()->json([
                'message' => "You have reached the maximum number of outlets ($maxOutlets) for your plan. Please upgrade to add more outlets.",
                'code' => 'OUTLET_LIMIT_REACHED',
                'current_plan' => $subscription?->plan->name,
                'max_outlets' => $maxOutlets,
            ], 403);
        }

        $outlet = Outlet::create([
            'name' => $request->name,
            // business_id auto-filled by BelongsToBusiness trait
            'address' => $request->address,
            'phone' => $request->phone,
            'description' => $request->description,
        ]);

        return response()->json([
            'message' => 'Outlet added successfully',
            'data' => $outlet,
        ], 201);
    }

    // update outlet
    public function updateOutlet(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string',
            'address' => 'required|string',
        ]);

        // Use query() to respect global scope
        $outlet = Outlet::query()->findOrFail($id);

        // Verify outlet belongs to user's business
        if ($outlet->business_id !== $request->user()->business_id) {
            return response()->json([
                'message' => 'Unauthorized access to this outlet',
            ], 403);
        }

        $outlet->name = $request->name;
        $outlet->address = $request->address;
        $outlet->phone = $request->phone;
        $outlet->description = $request->description;
        $outlet->save();

        return response()->json([
            'message' => 'Outlet updated successfully',
            'data' => $outlet,
        ]);
    }

    // get outlets for business
    public function getOutlets($businessId)
    {
        // Verify user has access to this business
        if ($businessId != auth()->user()->business_id) {
            return response()->json([
                'message' => 'Unauthorized access to this business',
            ], 403);
        }

        $outlets = Outlet::where('business_id', $businessId)->get();

        return response()->json([
            'data' => $outlets,
        ]);
    }
}
