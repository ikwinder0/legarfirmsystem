<?php

namespace Database\Seeders;

use App\Models\Calculator;
use Illuminate\Database\Seeder;

class RefinanceLoanCalculator extends Seeder
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
                'id' => 5,
                'name' => 'Quotation (Loan - Refinance)',
                'type' => 'loan_refinance',
                'created_at' => now(),
                'updated_at' => now()
            ]
		];
		Calculator::insert($calculators);
    }
}
