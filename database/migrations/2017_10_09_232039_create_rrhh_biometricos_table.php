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
        Schema::table('rrhh_biometricos', function (Blueprint $table) {
          $table->increments('id');
          $table->integer('unidad_desconcentrada_id')->unsigned();

          $table->smallInteger('estado')->default('1')->unsigned();
          $table->string('ip', 20);
          $table->integer('internal_id')->unsigned();
          $table->integer('com_key')->unsigned();
          $table->integer('soap_port')->unsigned();
          $table->integer('udp_port')->unsigned();
          $table->string('encoding', 50);
          $table->string('description', 250);
          $table->timestamps();

          $table->foreign('unidad_desconcentrada_id')
            ->references('id')
            ->on('unidades_desconcentradas')
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
        Schema::table('rrhh_biometricos', function (Blueprint $table) {
            //
        });
    }
}
