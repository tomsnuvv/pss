<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFindingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('findings', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('status_id')->unsigned();
            $table->integer('severity_id')->unsigned()->nullable();
            $table->string('target_type');
            $table->integer('target_id')->unsigned();
            $table->string('child_target_type')->nullable();
            $table->integer('child_target_id')->unsigned()->nullable();
            $table->integer('installation_id')->unsigned()->nullable();
            $table->string('title');
            $table->text('details')->nullable();
            $table->string('uid')->nullable();
            $table->integer('vulnerability_id')->unsigned()->nullable();
            $table->integer('vulnerability_type_id')->unsigned();
            $table->integer('module_id')->unsigned()->nullable();
            $table->timestamps();
        });

        Schema::table('findings', function (Blueprint $table) {
            $table->foreign('module_id')->references('id')->on('modules')->onDelete('CASCADE');
            $table->foreign('status_id')->references('id')->on('finding_statuses')->onDelete('CASCADE');
            $table->foreign('severity_id')->references('id')->on('severities')->onDelete('SET NULL');
            $table->foreign('installation_id')->references('id')->on('installations')->onDelete('CASCADE');
            $table->foreign('vulnerability_id')->references('id')->on('vulnerabilities')->onDelete('SET NULL');
            #$table->foreign('vulnerability_type_id')->references('id')->on('vulnerability_types')->onDelete('SET NULL');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('findings');
    }
}
