<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCertificateHostTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('certificate_host', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('host_id')->nullable()->unsigned();
            $table->integer('certificate_id')->nullable()->unsigned();
        });

        Schema::table('certificate_host', function (Blueprint $table) {
            $table->foreign('host_id')->references('id')->on('hosts')->onDelete('CASCADE');
            $table->foreign('certificate_id')->references('id')->on('certificates')->onDelete('CASCADE');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('certificate_host');
    }
}
