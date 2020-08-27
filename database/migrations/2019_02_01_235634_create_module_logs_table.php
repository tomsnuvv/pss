<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateModuleLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('module_logs', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('status_id')->unsigned()->nullable();
            $table->integer('module_id')->unsigned();
            $table->string('model_type')->nullable();
            $table->integer('model_id')->unsigned()->nullable();
            $table->integer('results')->unsigned()->nullable();
            $table->datetime('executed_at')->nullable();
            $table->datetime('finished_at')->nullable();
            $table->integer('duration')->nullable();
            $table->text('details')->nullable();
        });

        Schema::table('module_logs', function (Blueprint $table) {
            $table->foreign('module_id')->references('id')->on('modules')->onDelete('CASCADE');
            $table->foreign('status_id')->references('id')->on('module_log_statuses')->onDelete('SET NULL');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('module_logs');
    }
}
