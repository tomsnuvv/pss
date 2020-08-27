<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateVulnerabilitiesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('vulnerabilities', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('type_id')->unsigned()->nullable();
            $table->integer('severity_id')->unsigned()->nullable();
            $table->string('title', 400)->index();
            $table->text('description')->nullable();
            $table->text('proof_of_concept')->nullable();
            $table->text('vulnerable_code')->nullable();
            $table->dateTime('date')->nullable();
            $table->timestamps();
        });

        Schema::table('vulnerabilities', function (Blueprint $table) {
            $table->foreign('severity_id')->references('id')->on('severities')->onDelete('SET NULL');
            $table->foreign('type_id')->references('id')->on('vulnerability_types')->onDelete('SET NULL');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('vulnerabilities');
    }
}
