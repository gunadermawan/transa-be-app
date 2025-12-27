<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Business;
use App\Models\Outlet;
use App\Models\User;
use App\Services\SubscriptionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function __construct(private SubscriptionService $subscriptionService) {}

    // register
    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string',
            'business_name' => 'required|string',
            'address' => 'required|string',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role_id' => 1,
        ]);

        // create a business for the user
        $business = Business::create([
            'name' => $request->business_name,
            'owner_id' => $user->id,
        ]);

        $user->business_id = $business->id;
        $user->save();

        // assign trial subscription to the business
        $this->subscriptionService->assignTrialSubscription($business);

        // create an outlet for the business
        $outlet = Outlet::create([
            'name' => $request->name,
            'business_id' => $business->id,
            'address' => $request->address,
        ]);

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'access_token' => $token,
            'data' => $user,
        ], 201);
    }

    // login
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        $user = User::where('email', $request->email)->first();

        if (! $user || ! Hash::check($request->password, $user->password)) {
            return response()->json([
                'message' => 'Invalid credentials',
            ], 401);
        }

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'success' => true,
            'code' => 200,
            'message' => 'Login berhasil',
            'access_token' => $token,
            'data' => $user,
        ]);
    }

    // logout
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Logged out',
        ]);
    }

    // me
    public function me(Request $request)
    {
        // get business and outlet
        $user = $request->user();
        $user->load('business', 'outlet', 'business.outlets', 'role');

        return response()->json([
            'data' => $user,
        ]);
    }

    // refresh
    public function refresh(Request $request)
    {
        $user = $request->user();
        $user->tokens()->delete();

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'access_token' => $token,
            'data' => $user,
        ]);
    }

    // get outlets by business
    public function getOutletsByBusiness(Request $request)
    {
        $user = $request->user();
        $outlets = $user->business->outlets;

        return response()->json([
            'data' => $outlets,
        ]);
    }

    // get outlet by user if owner or manager
    public function getOutletByUser(Request $request)
    {
        $user = $request->user();
        if ($user->role_id == 1) {
            $outlet = Outlet::where('business_id', $user->business_id)->first();
        } else {
            $outlet = Outlet::find($user->outlet_id);
        }

        return response()->json([
            'data' => $outlet,
        ]);
    }

    // get outlet by id
    public function getOutletById(Request $request, $id)
    {
        $outlet = Outlet::find($id);

        return response()->json([
            'data' => $outlet,
        ]);
    }

    // add manager
    public function addManager(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string',
            'outlet_id' => 'required|exists:outlets,id',
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
            'role_id' => 2,
            'outlet_id' => $request->outlet_id,
            'business_id' => $request->user()->business_id,
        ]);

        return response()->json([
            'data' => $user,
        ], 201);
    }

    // add staff
    public function addStaff(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string',
            'outlet_id' => 'required|exists:outlets,id',
            'business_id' => 'required|exists:businesses,id',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role_id' => 3,
            'outlet_id' => $request->outlet_id,
            'business_id' => $request->business_id,
        ]);

        return response()->json([
            'data' => $user,
        ], 201);
    }

    // get user by business
    public function getUsersByBusiness(Request $request)
    {
        $user = $request->user();
        $users = User::where('business_id', $user->business_id)->get();

        return response()->json([
            'data' => $users,
        ]);
    }
}
