<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $roles = [
            ['name' => 'super_admin'],
            ['name' => 'business_owner'],
            ['name' => 'manager'],
            ['name' => 'cashier'],
            ['name' => 'staff'],
        ];

        foreach ($roles as $role) {
            Role::firstOrCreate($role);
        }
    }
}
