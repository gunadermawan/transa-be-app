<?php

namespace Database\Seeders;

use App\Models\SubscriptionPlan;
use Illuminate\Database\Seeder;

class SubscriptionPlanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Delete old plans that are no longer used
        SubscriptionPlan::whereIn('name', ['Business', 'Enterprise'])->delete();

        // Update or create the correct plans
        $plans = [
            [
                'name' => 'Trial',
                'description' => 'Mulai dengan trial gratis, upgrade kapan saja tanpa komitmen jangka panjang',
                'price' => 0,
                'billing_cycle' => 'trial',
                'trial_days' => 14,
                'max_outlets' => 1,
                'max_users' => 2,
                'max_products' => 50,
                'max_transactions_per_month' => 100,
                'features' => json_encode([
                    '1 outlet',
                    '2 pengguna',
                    'Max 50 produk',
                    'Max 100 transaksi/bulan',
                    'Dashboard dasar',
                ]),
                'is_popular' => false,
                'sort_order' => 1,
                'status' => 'active',
            ],
            [
                'name' => 'Starter',
                'description' => 'Cocok untuk bisnis kecil yang baru memulai',
                'price' => 99000,
                'billing_cycle' => 'monthly',
                'trial_days' => 0,
                'max_outlets' => 1,
                'max_users' => 5,
                'max_products' => null,
                'max_transactions_per_month' => null,
                'features' => json_encode([
                    '1 outlet',
                    '5 pengguna',
                    'Produk unlimited',
                    'Transaksi unlimited',
                    'Laporan lengkap',
                ]),
                'is_popular' => false,
                'sort_order' => 2,
                'status' => 'active',
            ],
            [
                'name' => 'Professional',
                'description' => 'Paket paling populer untuk bisnis yang berkembang',
                'price' => 249000,
                'billing_cycle' => 'monthly',
                'trial_days' => 0,
                'max_outlets' => 3,
                'max_users' => 15,
                'max_products' => null,
                'max_transactions_per_month' => null,
                'features' => json_encode([
                    '3 outlet',
                    '15 pengguna',
                    'Semua fitur unlimited',
                    'Multi-outlet dashboard',
                    'Transfer stok antar outlet',
                ]),
                'is_popular' => true,
                'sort_order' => 3,
                'status' => 'active',
            ],
        ];

        foreach ($plans as $plan) {
            SubscriptionPlan::updateOrCreate(
                ['name' => $plan['name']],
                $plan
            );
        }
    }
}
