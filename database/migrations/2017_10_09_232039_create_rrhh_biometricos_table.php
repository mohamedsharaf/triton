<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRrhhBiometricosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('rrhh_biometricos', function (Blueprint $table) {
          $table->increments('id');
          $table->integer('unidad_desconcentrada_id')->unsigned();

          $table->smallInteger('estado')->default('1')->unsigned();
          $table->smallInteger('e_conexion')->default('1')->unsigned();
          $table->dateTime('fs_conexion')->nullable();
          $table->dateTime('fb_conexion')->nullable();
          $table->dateTime('f_log_asistencia')->nullable();
          $table->string('codigo_af', 10)->nullable();
          $table->string('ip', 20)->nullable();
          $table->integer('internal_id')->unsigned()->nullable();
          $table->integer('com_key')->unsigned()->nullable();
          $table->integer('soap_port')->unsigned()->nullable();
          $table->integer('udp_port')->unsigned()->nullable();
          $table->string('encoding', 50)->nullable();
          $table->string('description', 250)->nullable();

          $table->timestamps();

          $table->foreign('unidad_desconcentrada_id')
            ->references('id')
            ->on('inst_unidades_desconcentradas')
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
        Schema::dropIfExists('rrhh_biometricos');
    }
}
