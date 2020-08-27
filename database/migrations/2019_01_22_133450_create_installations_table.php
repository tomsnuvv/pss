<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateInstallationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('installations', function (Blueprint $table) {
            $table->increments('id');
            $table->string('source_type');
            $table->integer('source_id')->unsigned();
            $table->string('child_source_type')->nullable();
            $table->integer('child_source_id')->unsigned()->nullable();
            $table->integer('product_id')->unsigned();
            $table->integer('module_id')->unsigned();
            $table->string('version')->nullable();
            $table->string('title')->nullable();
            $table->timestamps();
            $table->unique(['source_type', 'source_id','product_id']);
        });

        Schema::table('installations', function (Blueprint $table) {
            $table->foreign('product_id')->references('id')->on('products')->onDelete('CASCADE');
            $table->foreign('module_id')->references('id')->on('modules')->onDelete('CASCADE');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('installations');
    }
}
