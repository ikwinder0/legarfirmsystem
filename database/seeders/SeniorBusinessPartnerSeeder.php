<?php

namespace Database\Seeders;

use Backpack\PermissionManager\app\Models\Role;
use Illuminate\Database\Seeder;

class SeniorBusinessPartnerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Role::create([
            'name' => 'Senior Business Partner',
            'guard_name' => 'web'
        ]);

        $super_admin_user = \App\Models\User::create([
            'name' => 'Senior Business Partner 01',
            'email' => 'seniorbusinesspartner01@email.com',
            'password' => bcrypt('seniorbusinesspartner01@email.com')
        ]);

        $super_admin_user->assignRole("Senior Business Partner");
    }
}
