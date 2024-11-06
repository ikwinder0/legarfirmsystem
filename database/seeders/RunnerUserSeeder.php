<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Backpack\PermissionManager\app\Models\Role;

class RunnerUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Role::create([
            'name' => 'Runner',
            'guard_name' => 'web'
        ]);

        $runner_user = \App\Models\User::create([
            'name' => 'Runner User',
            'email' => 'runner@email.com',
            'password' => bcrypt('123456')
        ]);

        $runner_user->assignRole("Runner");
    }
}
