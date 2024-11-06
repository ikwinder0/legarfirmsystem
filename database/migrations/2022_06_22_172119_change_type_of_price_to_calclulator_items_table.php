<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangeTypeOfPriceToCalclulatorItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement("ALTER TABLE calculator_items MODIFY type_of_price ENUM('fix_price', 'memo_of_transfer', 'property_legal_fee', 'pax', 'min_pp') DEFAULT 'fix_price'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement("ALTER TABLE calculator_items MODIFY type_of_price ENUM('fix_price', 'memo_of_transfer', 'property_legal_fee', 'pax')");
    }
}
