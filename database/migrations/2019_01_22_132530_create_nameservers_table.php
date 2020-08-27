<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateNameserversTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('nameservers', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('domain_id')->unsigned()->nullable();
            $table->integer('host_id')->unsigned()->nullable();
            $table->string('name');
            $table->timestamps();
        });

        Schema::table('nameservers', function (Blueprint $table) {
            $table->foreign('domain_id')->references('id')->on('domains')->onDelete('CASCADE');
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
        Schema::dropIfExists('nameservers');
    }
}
