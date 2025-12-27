<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\RegisterStep1Request;
use App\Http\Requests\RegisterStep2Request;
use App\Models\Business;
use App\Models\BusinessSubscription;
use App\Models\Outlet;
use App\Models\SubscriptionPlan;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class RegisterController extends Controller
{
    /**
     * Show step 1 form (User Information).
     */
    public function showStep1()
    {
        return view('auth.register-step1');
    }

    /**
     * Process step 1 and store in session.
     */
    public function processStep1(RegisterStep1Request $request)
    {
        // Store step 1 data in session
        $request->session()->put('registration.step1', $request->validated());

        return redirect()->route('register.step2');
    }

    /**
     * Show step 2 form (Business Information).
     */
    public function showStep2(Request $request)
    {
        // Check if step 1 is completed
        if (! $request->session()->has('registration.step1')) {
            return redirect()->route('register.step1')
                ->with('error', 'Silakan lengkapi data akun Anda terlebih dahulu.');
        }

        return view('auth.register-step2');
    }

    /**
     * Process step 2 and complete registration.
     */
    public function processStep2(RegisterStep2Request $request)
    {
        // Check if step 1 is completed
        if (! $request->session()->has('registration.step1')) {
            return redirect()->route('register.step1')
                ->with('error', 'Silakan lengkapi data akun Anda terlebih dahulu.');
        }

        // Get step 1 data
        $step1Data = $request->session()->get('registration.step1');
        $step2Data = $request->validated();

        try {
            DB::beginTransaction();

            // 1. Create User (Owner)
            $user = User::create([
                'name' => $step1Data['name'],
                'email' => $step1Data['email'],
                'phone' => $step1Data['phone'],
                'password' => Hash::make($step1Data['password']),
                'role_id' => 2, // business_owner role
            ]);

            // 2. Create Business
            $business = Business::create([
                'name' => $step2Data['business_name'],
                'owner_id' => $user->id,
                'address' => $step2Data['address'],
                'phone' => $step2Data['business_phone'] ?? $step1Data['phone'],
                'email' => $step2Data['business_email'] ?? $step1Data['email'],
                'status' => 'active',
                'subscription_status' => 'trial',
                'activated_at' => now(),
                'expired_at' => now()->addDays(14), // 14-day trial
            ]);

            // 3. Create Default Outlet
            $outlet = Outlet::create([
                'name' => $step2Data['outlet_name'] ?? $step2Data['business_name'].' - Pusat',
                'business_id' => $business->id,
                'address' => $step2Data['address'],
                'phone' => $step2Data['business_phone'] ?? $step1Data['phone'],
                'description' => 'Outlet utama',
            ]);

            // 4. Get Trial Plan (or create if not exists)
            $trialPlan = SubscriptionPlan::firstOrCreate(
                ['name' => 'Trial'],
                [
                    'description' => 'Trial gratis 14 hari dengan akses fitur dasar',
                    'price' => 0,
                    'billing_cycle' => 'once',
                    'trial_days' => 14,
                    'max_outlets' => 1,
                    'max_users' => 2,
                    'max_products' => 50,
                    'max_transactions_per_month' => 100,
                    'features' => json_encode([
                        '1 outlet',
                        '2 users',
                        'Max 50 products',
                        'Max 100 transactions/month',
                        'Dashboard dasar',
                    ]),
                    'is_popular' => false,
                    'sort_order' => 0,
                    'status' => 'active',
                ]
            );

            // 5. Create Trial Subscription
            $subscription = BusinessSubscription::create([
                'business_id' => $business->id,
                'subscription_plan_id' => $trialPlan->id,
                'start_date' => now()->toDateString(),
                'end_date' => now()->addDays(14)->toDateString(),
                'trial_ends_at' => now()->addDays(14)->toDateString(),
                'status' => 'trial',
                'auto_renew' => false,
            ]);

            // 6. Update Business with subscription
            $business->update([
                'current_subscription_id' => $subscription->id,
            ]);

            // 7. Update User with business_id and outlet_id
            $user->update([
                'business_id' => $business->id,
                'outlet_id' => $outlet->id,
            ]);

            DB::commit();

            // Clear registration session
            $request->session()->forget('registration');

            // Login the user
            Auth::login($user);

            return redirect()->route('filament.admin.pages.dashboard')
                ->with('success', 'Selamat datang! Trial 14 hari Anda telah dimulai. 🎉');

        } 
        catch (\Exception $e) {
            DB::rollBack();

            return redirect()->back()
                ->withInput()
                ->with('error', 'Terjadi kesalahan saat membuat akun. Silakan coba lagi.');
        }
    }

    /**
     * Go back to step 1.
     */
    public function backToStep1(Request $request)
    {
        // Keep step 1 data but go back to edit
        return redirect()->route('register.step1');
    }
}
