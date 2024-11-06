<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Backpack\PermissionManager\app\Models\Role;

class AddUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $super_admin_user = \App\Models\User::create([
            'name' => 'Test Business Partner',
            'email' => 'businesspartner01@swot.com.my',
            'password' => bcrypt('businesspartner01@swot.com.my')
        ]);

        $super_admin_user->assignRole("Business Partner");
    }
}
