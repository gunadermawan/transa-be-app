<?php

namespace Database\Seeders;

use App\Models\Business;
use App\Models\Outlet;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        // Create temporary user first (for business owner_id constraint)
        $tempUser = User::create([
            'name' => 'Temp',
            'email' => 'temp@temp.com',
            'password' => Hash::make('password'),
            'role_id' => 1,
        ]);

        // Create demo business
        $business = Business::create([
            'name' => 'JagoFlutter Academy',
            'owner_id' => $tempUser->id,
        ]);

        // Create main outlet
        $outlet = Outlet::create([
            'business_id' => $business->id,
            'name' => 'Outlet Pusat',
            'phone' => '081234567890',
            'address' => 'Jl. Laravel No. 12, Jakarta',
        ]);

        // Update temp user to become super admin
        $tempUser->update([
            'name' => 'Super Admin',
            'email' => 'admin@jagoflutter.com',
            'business_id' => $business->id,
            'outlet_id' => $outlet->id,
        ]);

        $this->command->info('✅ Super Admin created:');
        $this->command->info('   Email: admin@jagoflutter.com');
        $this->command->info('   Password: password');
        $this->command->info('   Business: '.$business->name);
        $this->command->info('   Outlet: '.$outlet->name);
    }
}
