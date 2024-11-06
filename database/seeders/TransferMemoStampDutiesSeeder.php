<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class TransferMemoStampDutiesSeeder extends Seeder
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
              'max_price' => 100 * $k,
              'rate' => 1
          ],
          [
              'min_price' => 100 * $k + 1,
              'max_price' => 500 * $k,
              'rate' => 2
          ],
          [
              'min_price' => 500 * $k + 1,
              'max_price' => 1000 * $k,
              'rate' => 3
          ],
          [
              'min_price' => 1000 * $k + 1,
              'max_price' => 1000 * $k * $k * $k * $k * $k * $k * $k * $k * $k * $k,
              'rate' => 4
          ],
      ];

      \App\Models\TransferMemoStampDuty::insert($ranges);
    }
}
