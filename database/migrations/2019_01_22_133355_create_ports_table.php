<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePortsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ports', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('host_id')->unsigned();
            $table->string('protocol')->nullable();
            $table->integer('port');
            $table->string('service')->nullable();
            $table->timestamps();
            $table->unique(['host_id','port']);
        });

        Schema::table('ports', function (Blueprint $table) {
            $table->foreign('host_id')->references('id')->on('hosts')->onDelete('CASCADE');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('ports');
    }
}
