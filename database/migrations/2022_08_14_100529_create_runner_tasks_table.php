<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRunnerTasksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('runner_tasks', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('user_id');
			$table->string('title');
            $table->text('description')->nullable();
            $table->string('remarks');
			$table->json('attachments')->nullable();
            $table->string('status')->default('Process');
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
        Schema::dropIfExists('runner_tasks');
    }
}
