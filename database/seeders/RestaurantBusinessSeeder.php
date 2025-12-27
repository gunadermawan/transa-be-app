<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class RestaurantBusinessSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('🍽️  Creating Restaurant Business...');

        // Step 1: Create Owner User first
        $this->command->info("\n[1/3] Creating Restaurant Owner...");

        $owner = \App\Models\User::create([
            'name' => 'Restaurant Owner',
            'email' => 'resto@jagopos.com',
            'phone' => '081234567890',
            'password' => Hash::make('password123'),
            'role_id' => 2, // business_owner
        ]);

        $this->command->info("✓ Owner created: {$owner->name} ({$owner->email})");

        // Step 2: Create Business
        $this->command->info("\n[2/3] Creating Restaurant Business...");

        $business = \App\Models\Business::create([
            'name' => 'Warung Jago Rasa',
            'address' => 'Jl. Sudirman No. 45, Jakarta Pusat',
            'phone' => '021-5557890',
            'email' => 'info@warungjago.com',
            'tax_id' => '01.987.654.3-210.000',
            'owner_id' => $owner->id,
            'subscription_status' => 'trial',
            'status' => 'active',
        ]);

        $this->command->info("✓ Business created: {$business->name} (ID: {$business->id})");

        // Update owner's business_id
        $owner->update(['business_id' => $business->id]);
        $this->command->info('✓ Owner linked to business');

        // Step 3: Create Outlet
        $this->command->info("\n[3/3] Creating Restaurant Outlet...");

        $outlet = \App\Models\Outlet::create([
            'name' => 'Warung Jago Rasa - Pusat',
            'business_id' => $business->id,
            'address' => 'Jl. Sudirman No. 45, Jakarta Pusat',
            'phone' => '021-5557890',
            'description' => 'Outlet utama di Jakarta Pusat',
        ]);

        $this->command->info("✓ Outlet created: {$outlet->name} (ID: {$outlet->id})");

        // Update owner's outlet_id
        $owner->update(['outlet_id' => $outlet->id]);
        $this->command->info('✓ Owner linked to outlet');

        $this->command->info("\n╔════════════════════════════════════════════╗");
        $this->command->info('║     ✓ RESTAURANT BUSINESS CREATED!         ║');
        $this->command->info('╚════════════════════════════════════════════╝');

        $this->command->info("\n📋 Login Credentials:");
        $this->command->info("   Business: {$business->name}");
        $this->command->info("   Email: {$owner->email}");
        $this->command->info('   Password: password123');
        $this->command->info("\n🏪 Business Details:");
        $this->command->info("   Business ID: {$business->id}");
        $this->command->info("   Owner ID: {$owner->id}");
        $this->command->info("   Outlet ID: {$outlet->id}");
        $this->command->info("\n💡 Next Step: Run DummyDataSeeder to populate products!");
    }
}
