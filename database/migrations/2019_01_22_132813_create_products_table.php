<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('products', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('vendor_id')->unsigned()->nullable();
            $table->integer('type_id')->unsigned()->nullable();
            $table->integer('license_id')->unsigned()->nullable();
            $table->string('code');
            $table->string('name')->nullable();
            $table->text('description')->nullable();
            $table->string('website')->nullable();
            $table->string('source')->nullable();
            $table->string('latest_version')->nullable();
            $table->timestamp('latest_info_check')->nullable();
            $table->timestamps();
        });

        Schema::table('products', function (Blueprint $table) {
            $table->foreign('license_id')->references('id')->on('product_licenses')->onDelete('SET NULL');
            $table->foreign('type_id')->references('id')->on('product_types')->onDelete('SET NULL');
            $table->foreign('vendor_id')->references('id')->on('vendors')->onDelete('CASCADE');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('products');
    }
}
