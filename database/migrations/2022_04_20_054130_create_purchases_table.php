<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePurchasesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('purchases', function (Blueprint $table) {
            $table->id();
            $table->string('purchaser');
            $table->string('property');
            $table->double('purchase_price');
            $table->double('pax');
            $table->double('sale_purchase_fees');
            $table->double('ckht_2a');
            $table->double('transfer_memo_fee');
            $table->double('transfer_memo_stamp_duty');
            $table->double('other_fees');
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
        Schema::dropIfExists('purchases');
    }
}
