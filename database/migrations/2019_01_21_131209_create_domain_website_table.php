<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDomainWebsiteTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('domain_website', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('domain_id')->nullable()->unsigned();
            $table->integer('website_id')->nullable()->unsigned();
        });

        Schema::table('domain_website', function (Blueprint $table) {
            $table->foreign('domain_id')->references('id')->on('domains')->onDelete('CASCADE');
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
        Schema::dropIfExists('domain_website');
    }
}
