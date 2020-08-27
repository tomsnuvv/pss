<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDomainsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('domains', function (Blueprint $table) {
            $table->increments('id');
            $table->boolean('key')->nullable()-> default(0);
            $table->boolean('wildcard')->nullable()-> default(0);
            $table->string('name')->unique();
            $table->integer('domain_id')->nullable()->unsigned();
            $table->integer('host_id')->nullable()->unsigned();
            $table->integer('certificate_id')->nullable()->unsigned();
            $table->timestamps();
        });

        Schema::table('domains', function (Blueprint $table) {
            $table->foreign('host_id')->references('id')->on('hosts')->onDelete('SET NULL');
            $table->foreign('domain_id')->references('id')->on('domains')->onDelete('CASCADE');
            $table->foreign('certificate_id')->references('id')->on('certificates')->onDelete('SET NULL');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('domains');
    }
}
