<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateHostsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('hosts', function (Blueprint $table) {
            $table->increments('id');
            $table->boolean('key')->nullable()-> default(0);
            $table->integer('type_id')->nullable()->unsigned();
            $table->string('name')->nullable();
            $table->string('ip')->unique();
            $table->timestamps();
        });

        Schema::table('hosts', function (Blueprint $table) {
            $table->foreign('type_id')->references('id')->on('host_types')->onDelete('CASCADE');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('hosts');
    }
}
