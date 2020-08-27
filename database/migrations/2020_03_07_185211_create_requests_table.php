<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRequestsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('requests', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('website_id')->unsigned()->index();
            $table->integer('content_id')->unsigned()->nullable();
            $table->string('method');
            $table->string('path');
            $table->text('parameters')->nullable();
            $table->integer('status')->nullable();
            $table->timestamps();
        });

        Schema::table('requests', function (Blueprint $table) {
            $table->foreign('website_id')->references('id')->on('websites')->onDelete('CASCADE');
            $table->foreign('content_id')->references('id')->on('request_contents')->onDelete('SET NULL');
            $table->unique(['website_id','method', 'path']);
        });

        if (env('DB_CONNECTION') !== 'sqlite') {
            DB::unprepared('ALTER TABLE `requests` CONVERT TO CHARACTER SET utf8mb4');
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('requests');
    }
}
