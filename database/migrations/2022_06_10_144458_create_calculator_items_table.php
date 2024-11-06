<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCalculatorItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('calculator_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('cid');
            $table->enum('section', ['professional_charges', 'reimbursements', 'disbursements']);
            $table->enum('type_of_price', ['fix_price', 'memo_of_transfer', 'property_legal_fee', 'pax']);
            $table->string('name');
            $table->string('label');
            $table->double('price')->nullable();
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
        Schema::dropIfExists('calculator_items');
    }
}
