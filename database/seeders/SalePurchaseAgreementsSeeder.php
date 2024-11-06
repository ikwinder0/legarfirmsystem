<?php

namespace Database\Seeders;

use App\Models\SalePurchaseAgreement;
use Illuminate\Database\Seeder;

class SalePurchaseAgreementsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $k = 1000;
        $created = now();
        $ranges = [
            [
                'min_price' => 1,
                'max_price' => 500 * $k,
                'fees_rate' => 1,
                'created_at' => $created
            ],
            [
                'min_price' => 500 * $k + 1,
                'max_price' => 1 * $k * $k,
                'fees_rate' => 0.8,
                'created_at' => $created
            ],
            [
                'min_price' => 1 * $k * $k + 1,
                'max_price' => 3 * $k * $k,
                'fees_rate' => 0.7,
                'created_at' => $created
            ],
            [
                'min_price' => 3 * $k * $k + 1,
                'max_price' => 5 * $k * $k,
                'fees_rate' => 0.6,
                'created_at' => $created
            ],
            [
                'min_price' => 5 * $k * $k + 1,
                'max_price' => 1 * $k * $k * $k * $k * $k * $k * $k * $k,
                'fees_rate' => 0.5,
                'created_at' => $created
            ]
        ];

        SalePurchaseAgreement::insert($ranges);
    }
}
