<?php

namespace Database\Seeders;

use App\Models\Calculator;
use Illuminate\Database\Seeder;

class CalculatorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $calculators = [
            [
                'id' => 1,
                'name' => 'Quotation (SPA)',
                'type' => 'spa',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'id' => 2,
                'name' => 'Quotation (Loan)',
                'type' => 'loan',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'id' => 3,
                'name' => 'Quotation (Master Title Loan)',
                'type' => 'master_title_loan',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'id' => 4,
                'name' => 'Quotation (Cost of Assist Vendor)',
                'type' => 'cost_of_assist_vendor',
                'created_at' => now(),
                'updated_at' => now()
            ],
        ];

        Calculator::insert($calculators);
    }
}
