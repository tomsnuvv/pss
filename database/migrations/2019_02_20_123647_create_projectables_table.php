<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProjectablesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('projectables', function (Blueprint $table) {
            $table->integer('project_id')->unsigned();
            $table->string('projectable_type');
            $table->integer('projectable_id')->unsigned();
        });

        Schema::table('projectables', function (Blueprint $table) {
            $table->foreign('project_id')->references('id')->on('projects')->onDelete('CASCADE');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('projectables');
    }
}
