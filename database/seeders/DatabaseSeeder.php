<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $user = \App\Models\User::create([
            'name' => 'Test Admin',
            'email' => 'admin@email.com',
            'password' => bcrypt('123456')
        ]);

        $user->roles()->create([
            'name' => 'Admin',
            'guard_name' => 'web'
        ]);

        $user = \App\Models\User::create([
            'name' => 'Business partner',
            'email' => 'business@email.com',
            'password' => bcrypt('123456')
        ]);

        $user->roles()->create([
            'name' => 'Business Partner',
            'guard_name' => 'web'
        ]);

        $user = \App\Models\User::create([
            'name' => 'Customer',
            'email' => 'customer@email.com',
            'password' => bcrypt('123456')
        ]);

        $user->roles()->create([
            'name' => 'Customer',
            'guard_name' => 'web'
        ]);

        $this->call([
            SalePurchaseAgreementsSeeder::class,
            TransferMemoStampDutiesSeeder::class,
            TransferMemosSeeder::class,
            StampDutyFASeeder::class,
            GuestRoleSeeder::class,
            SuperAdminSeeder::class,
            CalculatorSeeder::class,
            CalculatorItemSeeder::class,
            RefinanceLoanCalculatorItem::class,
            RefinanceLoanCalculator::class,
            FacilityAgreementSeeder::class,
			RunnerUserSeeder::class,
        ]);
    }
}
