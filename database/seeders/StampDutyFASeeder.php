<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Backpack\Settings\app\Models\Setting;

class StampDutyFASeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Setting::create([
            "key" => "sd_fa",
            "name" => "Stamp Duty on Facility Agreement",
            "description" => "This value is the rate(percentage) of fees for the loan amount on facility Agreement.",
            "value" => "0.5",
            "field" => '{"name":"value","label":"Value","type":"text"}',
            "active" => 1
        ]);
    }
}
