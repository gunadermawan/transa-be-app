<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\SubscriptionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class StaffController extends Controller
{
    public function __construct(private SubscriptionService $subscriptionService) {}

    // add staff to outlet
    public function addStaff(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string',
            'outlet_id' => 'required|integer',
            'role_id' => 'required|integer',
        ]);

        $business = $request->user()->business;

        // Check subscription limit
        if (! $this->subscriptionService->canAddUser($business)) {
            $subscription = $this->subscriptionService->getActiveSubscription($business);
            $maxUsers = $subscription?->plan->max_users ?? 0;

            return response()->json([
                'message' => "You have reached the maximum number of users ($maxUsers) for your plan. Please upgrade to add more users.",
                'code' => 'USER_LIMIT_REACHED',
                'current_plan' => $subscription?->plan->name,
                'max_users' => $maxUsers,
            ], 403);
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role_id' => $request->role_id,
            'outlet_id' => $request->outlet_id,
            'business_id' => $request->user()->business_id,
        ]);

        return response()->json([
            'data' => $user,
        ], 201);
    }

    // get all staff for business
    public function getStaff($businessId)
    {
        // Verify user has access to this business
        if ($businessId != auth()->user()->business_id) {
            return response()->json([
                'message' => 'Unauthorized access to this business',
            ], 403);
        }

        $staff = User::where('business_id', $businessId)->get();

        // load role and outlet
        $staff->load('role', 'outlet');

        return response()->json([
            'data' => $staff,
        ]);
    }

    // edit role staff
    public function editStaff(Request $request, $id)
    {
        $request->validate([
            'role_id' => 'required|integer',
            'name' => 'required|string',
            'email' => 'required|email',
        ]);

        $user = User::findOrFail($id);

        // Verify staff belongs to user's business
        if ($user->business_id !== $request->user()->business_id) {
            return response()->json([
                'message' => 'Unauthorized access to this staff',
            ], 403);
        }

        $user->role_id = $request->role_id;
        $user->name = $request->name;
        $user->email = $request->email;
        $user->outlet_id = $request->outlet_id;

        if ($request->password) {
            $user->password = Hash::make($request->password);
        }

        $user->save();

        return response()->json([
            'message' => 'Staff updated successfully',
            'data' => $user,
        ]);
    }
}
