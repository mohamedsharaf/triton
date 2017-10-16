<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUbgeProvinciasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ubge_provincias', function (Blueprint $table) {
          $table->increments('id');
          $table->integer('departamento_id')->unsigned();

          $table->smallInteger('estado')->default('1')->unsigned();
          $table->string('codigo', 4)->unique()->nullable();
          $table->string('nombre', 250)->nullable();

          $table->foreign('departamento_id')
            ->references('id')
            ->on('ubge_departamentos')
            ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('ubge_provincias');
    }
}
