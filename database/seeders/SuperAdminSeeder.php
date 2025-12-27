<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class SuperAdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get super_admin role
        $superAdminRole = Role::where('name', 'super_admin')->first();

        if (! $superAdminRole) {
            $this->command->error('Super Admin role not found. Please run RoleSeeder first.');

            return;
        }

        // Check if super admin already exists
        $existingSuperAdmin = User::where('role_id', $superAdminRole->id)->first();

        if ($existingSuperAdmin) {
            $this->command->info('Super Admin already exists.');

            return;
        }

        // Create Super Admin
        User::create([
            'name' => 'Super Administrator',
            'email' => 'superadmin@possaas.com',
            'password' => Hash::make('password'),
            'role_id' => $superAdminRole->id,
            'phone' => '081999999999',
            'business_id' => null, // Super admin is not tied to any business
            'outlet_id' => null,
        ]);

        $this->command->info('Super Admin created successfully!');
        $this->command->info('Email: superadmin@possaas.com');
        $this->command->info('Password: password');
    }
}
