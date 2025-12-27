<?php

namespace App\Http\Controllers;

use App\Models\SubscriptionPlan;

class LandingController extends Controller
{
    public function index()
    {
        $plans = SubscriptionPlan::where('status', 'active')
            ->orderBy('sort_order')
            ->get();

        return view('landing', compact('plans'));
    }
}
