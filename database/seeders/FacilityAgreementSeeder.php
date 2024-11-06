<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Backpack\Settings\app\Models\Setting;

class FacilityAgreementSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        if (Setting::get('fa')) {
            Setting::where('key', 'fa')->first()->update([
                "value" => "1"
            ]);
        } else {
            Setting::create([
                "key" => "fa",
                "name" => "Facility Agreement",
                "description" => "This value is the rate(percentage) on loan amount for facility Agreement.",
                "value" => "1",
                "field" => '{"name":"value","label":"Value","type":"text"}',
                "active" => 1
            ]);
        }
    }
}
