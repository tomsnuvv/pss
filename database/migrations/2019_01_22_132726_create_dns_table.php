<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDnsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('dns', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('domain_id')->unsigned()->nullable();
            $table->string('type');
            $table->text('value')->nullable();
            $table->string('target_type')->nullable();
            $table->integer('target_id')->unsigned()->nullable();
            $table->string('class')->nullable();
            $table->string('ttl')->nullable();
            $table->string('pri')->nullable();
            $table->timestamps();
        });

        Schema::table('dns', function (Blueprint $table) {
            $table->foreign('domain_id')->references('id')->on('domains')->onDelete('CASCADE');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('dns');
    }
}
