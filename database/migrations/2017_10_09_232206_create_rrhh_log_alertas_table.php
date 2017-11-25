<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRrhhLogAlertasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('rrhh_log_alertas', function (Blueprint $table) {
          $table->increments('id');
          $table->integer('biometrico_id')->unsigned();

          $table->smallInteger('tipo_emisor')->unsigned()->nullable();
          $table->smallInteger('tipo_alerta')->unsigned()->nullable();
          $table->dateTime('f_alerta')->nullable();
          $table->text('mensaje')->nullable();

          $table->timestamps();

          $table->foreign('biometrico_id')
            ->references('id')
            ->on('rrhh_biometricos')
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
        Schema::dropIfExists('rrhh_log_alertas');
    }
}
