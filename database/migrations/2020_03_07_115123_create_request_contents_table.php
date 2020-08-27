<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRequestContentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('request_contents', function (Blueprint $table) {
            $table->increments('id');
            $table->string('hash')->unique();
            $table->longtext('body');
            $table->timestamps();
        });

        if (env('DB_CONNECTION') !== 'sqlite') {
            DB::unprepared('ALTER TABLE `request_contents` CONVERT TO CHARACTER SET utf8mb4');
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('request_contents');
    }
}
