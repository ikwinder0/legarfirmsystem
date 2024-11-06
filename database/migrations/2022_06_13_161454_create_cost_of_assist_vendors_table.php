<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCostOfAssistVendorsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cost_of_assist_vendors', function (Blueprint $table) {
            $table->id();
            $table->string('purchaser');
            $table->string('property');
            $table->double('purchase_price');
            $table->double('pax');
            $table->double('ckht_1a');
            $table->double('ckht_3');
            $table->double('bankruptcy_search');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('cost_of_assist_vendors');
    }
}
