<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCertificatesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('certificates', function (Blueprint $table) {
            $table->increments('id');
            $table->text('name');
            $table->string('subject_common_name')->nullable();
            $table->string('subject_org_unit')->nullable();
            $table->string('issuer_common_name')->nullable();
            $table->string('issuer_org')->nullable();
            $table->string('issuer_country')->nullable();
            $table->string('issuer_county')->nullable();
            $table->string('issuer_locality')->nullable();
            $table->dateTime('creation_date');
            $table->dateTime('expiration_date');
            $table->string('key_type')->nullable();
            $table->integer('key_length')->unsigned()->nullable();
            $table->string('serial')->nullable();
            $table->string('signature_algorithm')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('certificates');
    }
}
