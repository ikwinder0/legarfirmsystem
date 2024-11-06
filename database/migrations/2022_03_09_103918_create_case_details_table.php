<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCaseDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('case_details', function (Blueprint $table) {
            $table->id();
            $table->string('title');
             $table->text('description')->nullable();
             $table->json('softcopy')->nullable();
            $table->double('price')->nullable();
            $table->string('status')->default('NEW');
            $table->unsignedBigInteger('introduced_by')->nullable();
            $table->unsignedBigInteger('customer')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('case_details');
    }
}
