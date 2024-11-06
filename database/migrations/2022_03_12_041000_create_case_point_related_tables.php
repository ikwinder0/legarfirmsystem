<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCasePointRelatedTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->double('case_points')->default(0);
        });
        Schema::create('case_point_transactions', function (Blueprint $table) {
            $table->id();
            $table->double('price')->nullable();
            $table->integer('case_point')->default(0);
            $table->integer('old_points')->default(0);
            $table->integer('updated_current_points')->default(0);
            $table->unsignedBigInteger('business_partner')->nullable();
            $table->unsignedBigInteger('customer_id')->nullable();
            $table->unsignedBigInteger('created_by');
            $table->string('status',30)->nullable();
            $table->text('remarks')->nullable();
            $table->json('case_detail')->nullable();
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
        Schema::dropIfExists('case_point_transactions');
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('case_points');
        });
    }
}
