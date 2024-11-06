<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRefinanceLoansTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('refinance_loans', function (Blueprint $table) {
            $table->id();
			$table->string('purchaser');
            $table->string('property');
            $table->double('pax');
            $table->double('loan_amount');
            $table->double('facility_agreement');
            $table->double('memo_charge');
            $table->double('stamp_duty_facility_agreement');
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
        Schema::dropIfExists('refinance_loans');
    }
}
