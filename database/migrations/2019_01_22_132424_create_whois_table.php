<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateWhoisTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('whois', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('domain_id')->unsigned()->nullable();
            $table->string('registrar')->nullable();
            $table->string('owner')->nullable();
            $table->dateTime('creation_date')->nullable();
            $table->dateTime('expiration_date')->nullable();
            $table->text('raw')->nullable();
            $table->timestamps();
        });

        Schema::table('whois', function (Blueprint $table) {
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
        Schema::dropIfExists('whois');
    }
}
