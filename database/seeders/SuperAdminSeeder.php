<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Backpack\PermissionManager\app\Models\Role;

class SuperAdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Role::create([
            'name' => 'Super Admin',
            'guard_name' => 'web'
        ]);

        $super_admin_user = \App\Models\User::create([
            'name' => 'Super Admin',
            'email' => 'sadmin@email.com',
            'password' => bcrypt('123456')
        ]);

        $super_admin_user->assignRole("Super Admin");
    }
}
