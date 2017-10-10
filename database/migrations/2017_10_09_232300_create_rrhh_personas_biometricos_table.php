<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRrhhPersonasBiometricosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('rrhh_personas_biometricos', function (Blueprint $table) {
          $table->increments('id');
          $table->integer('persona_id')->unsigned();
          $table->integer('biometrico_id')->unsigned();

          $table->smallInteger('estado')->default('1')->unsigned();
          $table->dateTime('f_registro_biometrico')->nullable();

          $table->timestamps();

          $table->foreign('persona_id')
            ->references('id')
            ->on('rrhh_personas')
            ->onDelete('cascade');

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
        Schema::dropIfExists('rrhh_personas_biometricos');
    }
}
