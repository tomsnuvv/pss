<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateWebsitesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('websites', function (Blueprint $table) {
            $table->increments('id');
            $table->boolean('key')->nullable()-> default(0);
            $table->boolean('crawl')->nullable()-> default(0);
            $table->string('url')->unique();
            $table->smallInteger('status')->nullable();
            $table->integer('environment_id')->unsigned()->nullable();
            $table->timestamps();
        });

        Schema::table('websites', function (Blueprint $table) {
            $table->foreign('environment_id')->references('id')->on('environments')->onDelete('CASCADE');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('websites');
    }
}
