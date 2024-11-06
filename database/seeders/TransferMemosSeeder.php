<?php

namespace Database\Seeders;

use App\Models\TransferMemo;
use Illuminate\Database\Seeder;

class TransferMemosSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $k = 1000;
        $ranges = [
            [
                'min_price' => 1,
                'max_price' => 25 * $k - 1,
                'amount' => 50
            ],
            [
                'min_price' => 25 * $k,
                'max_price' => 50 * $k,
                'amount' => 80
            ],
            [
                'min_price' => 50 * $k + 1,
                'max_price' => 100 * $k,
                'amount' => 150
            ],
            [
                'min_price' => 100 * $k + 1,
                'max_price' => 200 * $k,
                'amount' => 300
            ],
            [
                'min_price' => 200 * $k + 1,
                'max_price' => 300 * $k,
                'amount' => 600
            ],
            [
                'min_price' => 300 * $k + 1,
                'max_price' => 400 * $k,
                'amount' => 1500
            ],
            [
                'min_price' => 400 * $k + 1,
                'max_price' => 500 * $k,
                'amount' => 2000
            ],
        ];

        $start = 500 * $k;
        for ($i = 1; $i < 15; $i++) {
            $arr = [
                'min_price' => $start + 1,
                'max_price' => $start + 50 * $k,
                'amount' => 2000 + 100 * $i
            ];
            $start += 50 * $k;
        
            array_push($ranges, $arr);
        }

        TransferMemo::insert($ranges);
    }
}
