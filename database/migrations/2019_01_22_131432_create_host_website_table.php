<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateHostWebsiteTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('host_website', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('host_id')->nullable()->unsigned();
            $table->integer('website_id')->nullable()->unsigned();
        });

        Schema::table('host_website', function (Blueprint $table) {
            $table->foreign('host_id')->references('id')->on('hosts')->onDelete('CASCADE');
            $table->foreign('website_id')->references('id')->on('websites')->onDelete('CASCADE');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('host_website');
    }
}
