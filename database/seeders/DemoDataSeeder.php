<?php

namespace Database\Seeders;

use App\Models\Attendance;
use App\Models\Business;
use App\Models\BusinessSetting;
use App\Models\BusinessSubscription;
use App\Models\Category;
use App\Models\Customer;
use App\Models\Outlet;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\Role;
use App\Models\Shift;
use App\Models\Stock;
use App\Models\SubscriptionPlan;
use App\Models\Supplier;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DemoDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get roles
        $ownerRole = Role::where('name', 'business_owner')->first();
        $managerRole = Role::where('name', 'manager')->first();
        $cashierRole = Role::where('name', 'cashier')->first();
        $staffRole = Role::where('name', 'staff')->first();

        // Get subscription plan (Professional is the most popular plan)
        $businessPlan = SubscriptionPlan::where('name', 'Professional')->first();

        // Create Owner User FIRST (without business_id)
        $owner = User::create([
            'name' => 'Budi Santoso',
            'email' => 'owner@tokomajujaya.com',
            'password' => Hash::make('password'),
            'role_id' => $ownerRole->id,
            'phone' => '081234567890',
        ]);

        // NOW Create Demo Business with valid owner_id
        $business = Business::create([
            'name' => 'Toko Maju Jaya',
            'owner_id' => $owner->id,
            'address' => 'Jl. Raya Sudirman No. 123, Jakarta',
            'phone' => '021-5551234',
            'email' => 'info@tokomajujaya.com',
            'tax_id' => '01.234.567.8-901.000',
            'status' => 'active',
        ]);

        // Update owner with business_id
        $owner->update(['business_id' => $business->id]);

        // Create Business Subscription
        $subscription = BusinessSubscription::create([
            'business_id' => $business->id,
            'subscription_plan_id' => $businessPlan->id,
            'start_date' => now()->subDays(10),
            'end_date' => now()->addDays(20),
            'trial_ends_at' => now()->addDays(4),
            'next_billing_date' => now()->addDays(20),
            'status' => 'trial',
            'auto_renew' => true,
        ]);

        // Update business with subscription
        $business->update([
            'current_subscription_id' => $subscription->id,
            'subscription_status' => 'trial',
        ]);

        // Create Outlets
        $mainOutlet = Outlet::create([
            'name' => 'Cabang Utama - Jakarta',
            'business_id' => $business->id,
            'address' => 'Jl. Raya Sudirman No. 123, Jakarta',
            'phone' => '021-5551234',
            'description' => 'Toko utama di Jakarta',
        ]);

        $branchOutlet = Outlet::create([
            'name' => 'Cabang Bandung',
            'business_id' => $business->id,
            'address' => 'Jl. Braga No. 45, Bandung',
            'phone' => '022-4441234',
            'description' => 'Cabang di Bandung',
        ]);

        // Create Manager
        $manager = User::create([
            'name' => 'Siti Nurhaliza',
            'email' => 'manager@tokomajujaya.com',
            'password' => Hash::make('password'),
            'role_id' => $managerRole->id,
            'business_id' => $business->id,
            'outlet_id' => $mainOutlet->id,
            'phone' => '081234567891',
        ]);

        // Create Cashiers
        $cashier1 = User::create([
            'name' => 'Ahmad Yani',
            'email' => 'cashier1@tokomajujaya.com',
            'password' => Hash::make('password'),
            'role_id' => $cashierRole->id,
            'business_id' => $business->id,
            'outlet_id' => $mainOutlet->id,
            'phone' => '081234567892',
        ]);

        $cashier2 = User::create([
            'name' => 'Dewi Lestari',
            'email' => 'cashier2@tokomajujaya.com',
            'password' => Hash::make('password'),
            'role_id' => $cashierRole->id,
            'business_id' => $business->id,
            'outlet_id' => $branchOutlet->id,
            'phone' => '081234567893',
        ]);

        // Create Business Settings (Tax)
        $ppn = BusinessSetting::create([
            'business_id' => $business->id,
            'name' => 'PPN 11%',
            'charge_type' => 'percentage',
            'type' => 'tax',
            'value' => '11',
        ]);

        BusinessSetting::create([
            'business_id' => $business->id,
            'name' => 'Service Charge',
            'charge_type' => 'percentage',
            'type' => 'service',
            'value' => '5',
        ]);

        // Create Categories
        $electronics = Category::create([
            'name' => 'Electronics',
            'business_id' => $business->id,
        ]);

        $fashion = Category::create([
            'name' => 'Fashion',
            'business_id' => $business->id,
        ]);

        $food = Category::create([
            'name' => 'Food & Beverage',
            'business_id' => $business->id,
        ]);

        // Create Suppliers
        $supplier1 = Supplier::create([
            'business_id' => $business->id,
            'name' => 'PT. Elektronik Indonesia',
            'code' => 'SUP001',
            'contact_person' => 'Andi Wijaya',
            'phone' => '021-7778888',
            'email' => 'sales@elektronik.co.id',
            'address' => 'Jl. Industri No. 10, Jakarta',
            'payment_terms' => 'NET 30',
            'status' => 'active',
        ]);

        $supplier2 = Supplier::create([
            'business_id' => $business->id,
            'name' => 'CV. Fashionista',
            'code' => 'SUP002',
            'contact_person' => 'Lisa Permata',
            'phone' => '021-9998888',
            'email' => 'order@fashionista.com',
            'address' => 'Jl. Mode No. 25, Bandung',
            'payment_terms' => 'NET 14',
            'status' => 'active',
        ]);

        // Create Products
        $laptop = Product::create([
            'name' => 'Laptop Gaming XYZ',
            'category_id' => $electronics->id,
            'business_id' => $business->id,
            'supplier_id' => $supplier1->id,
            'tax_id' => $ppn->id,
            'description' => 'Laptop gaming dengan spesifikasi tinggi',
            'color' => 'Black',
            'price' => 15000000,
            'cost' => 12000000,
            'barcode' => '1234567890123',
            'sku' => 'LAP-XYZ-001',
            'unit_type' => 'pcs',
            'stock_minimum' => 5,
            'reorder_point' => 10,
            'optimal_stock_level' => 20,
            'product_type' => 'physical',
            'status' => 'active',
            'is_stock_managed' => true,
            'is_featured' => true,
        ]);

        // Create Stock for Laptop
        Stock::create([
            'product_id' => $laptop->id,
            'outlet_id' => $mainOutlet->id,
            'quantity' => 15,
        ]);

        Stock::create([
            'product_id' => $laptop->id,
            'outlet_id' => $branchOutlet->id,
            'quantity' => 8,
        ]);

        // Create T-Shirt with Variants
        $tshirt = Product::create([
            'name' => 'Basic T-Shirt',
            'category_id' => $fashion->id,
            'business_id' => $business->id,
            'supplier_id' => $supplier2->id,
            'tax_id' => $ppn->id,
            'description' => 'Kaos polos berkualitas',
            'price' => 100000,
            'cost' => 60000,
            'barcode' => '2234567890123',
            'sku' => 'TSH-BAS-001',
            'unit_type' => 'pcs',
            'stock_minimum' => 10,
            'reorder_point' => 20,
            'optimal_stock_level' => 50,
            'product_type' => 'physical',
            'status' => 'active',
            'is_stock_managed' => true,
            'is_featured' => true,
        ]);

        // Create T-Shirt Variants
        $variants = [
            ['S', 'White', 0],
            ['M', 'White', 0],
            ['L', 'White', 10000],
            ['XL', 'White', 20000],
            ['S', 'Black', 5000],
            ['M', 'Black', 5000],
            ['L', 'Black', 15000],
            ['XL', 'Black', 25000],
        ];

        foreach ($variants as $index => $variant) {
            [$size, $color, $adjustment] = $variant;
            ProductVariant::create([
                'product_id' => $tshirt->id,
                'variant_name' => "$size - $color",
                'sku' => "TSH-BAS-$size-$color",
                'barcode' => '223456789'.str_pad($index, 4, '0', STR_PAD_LEFT),
                'price_adjustment' => $adjustment,
                'cost_adjustment' => $adjustment * 0.6,
                'attributes' => json_encode(['size' => $size, 'color' => $color]),
                'sort_order' => $index,
                'status' => 'active',
            ]);
        }

        // Create more products
        $coffee = Product::create([
            'name' => 'Premium Coffee Beans',
            'category_id' => $food->id,
            'business_id' => $business->id,
            'description' => 'Kopi arabika premium',
            'price' => 85000,
            'cost' => 50000,
            'barcode' => '3234567890123',
            'sku' => 'COF-PRE-001',
            'unit_type' => 'kg',
            'stock_minimum' => 5,
            'reorder_point' => 10,
            'product_type' => 'physical',
            'status' => 'active',
            'is_stock_managed' => true,
        ]);

        Stock::create([
            'product_id' => $coffee->id,
            'outlet_id' => $mainOutlet->id,
            'quantity' => 25,
        ]);

        // Create Customers
        Customer::create([
            'business_id' => $business->id,
            'name' => 'Rina Kusuma',
            'phone' => '081345678901',
            'email' => 'rina@example.com',
            'address' => 'Jl. Merdeka No. 10, Jakarta',
            'loyalty_points' => 150,
            'total_spent' => 2500000,
            'visit_count' => 5,
            'last_visit_at' => now()->subDays(2),
            'birthdate' => '1990-05-15',
            'customer_group' => 'vip',
        ]);

        Customer::create([
            'business_id' => $business->id,
            'name' => 'Agus Prakoso',
            'phone' => '081345678902',
            'email' => 'agus@example.com',
            'address' => 'Jl. Kemang No. 20, Jakarta',
            'loyalty_points' => 50,
            'total_spent' => 750000,
            'visit_count' => 2,
            'last_visit_at' => now()->subDays(7),
            'birthdate' => '1985-08-22',
            'customer_group' => 'regular',
        ]);

        Customer::create([
            'business_id' => $business->id,
            'name' => 'Maya Putri',
            'phone' => '081345678903',
            'email' => 'maya@example.com',
            'loyalty_points' => 10,
            'total_spent' => 150000,
            'visit_count' => 1,
            'last_visit_at' => now()->subDays(15),
            'customer_group' => 'regular',
        ]);

        // Create Shifts
        Shift::create([
            'outlet_id' => $mainOutlet->id,
            'name' => 'Morning Shift',
            'start_time' => '08:00:00',
            'end_time' => '16:00:00',
            'grace_period_minutes' => 15,
            'status' => 'active',
        ]);

        Shift::create([
            'outlet_id' => $mainOutlet->id,
            'name' => 'Evening Shift',
            'start_time' => '16:00:00',
            'end_time' => '00:00:00',
            'grace_period_minutes' => 15,
            'status' => 'active',
        ]);

        $morningShiftBandung = Shift::create([
            'outlet_id' => $branchOutlet->id,
            'name' => 'Morning Shift',
            'start_time' => '09:00:00',
            'end_time' => '17:00:00',
            'grace_period_minutes' => 10,
            'status' => 'active',
        ]);

        // Create Attendance
        Attendance::create([
            'user_id' => $cashier1->id,
            'outlet_id' => $mainOutlet->id,
            'shift_id' => 1,
            'date' => now()->toDateString(),
            'clock_in' => '08:10:00',
            'clock_out' => '16:05:00',
            'status' => 'present',
        ]);

        Attendance::create([
            'user_id' => $cashier2->id,
            'outlet_id' => $branchOutlet->id,
            'shift_id' => $morningShiftBandung->id,
            'date' => now()->toDateString(),
            'clock_in' => '09:05:00',
            'status' => 'present',
        ]);

        $this->command->info('Demo data seeded successfully!');
        $this->command->info('');
        $this->command->info('=== Login Credentials ===');
        $this->command->info('Owner: owner@tokomajujaya.com / password');
        $this->command->info('Manager: manager@tokomajujaya.com / password');
        $this->command->info('Cashier 1: cashier1@tokomajujaya.com / password');
        $this->command->info('Cashier 2: cashier2@tokomajujaya.com / password');
    }
}
