<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCaseIdInCasePointTransactionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('case_point_transactions', function (Blueprint $table) {
            $table->unsignedBigInteger('case_id');
        });
        Schema::table('case_details', function (Blueprint $table) {
            $table->json('tracks')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('case_point_transactions', function (Blueprint $table) {
            $table->dropColumn('case_id');
        });
        Schema::table('case_details', function (Blueprint $table) {
            $table->dropColumn('tracks');
        });
    }
}
