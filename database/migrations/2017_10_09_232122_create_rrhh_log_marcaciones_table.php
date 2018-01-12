<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRrhhLogMarcacionesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('rrhh_log_marcaciones', function (Blueprint $table) {
          $table->increments('id');
          $table->integer('biometrico_id')->unsigned()->nullable();
          $table->integer('persona_id')->unsigned()->nullable();

          $table->smallInteger('estado')->default('1')->unsigned()->nullable();
          $table->smallInteger('tipo_marcacion')->default('1')->unsigned();
          $table->integer('n_documento_biometrico')->unsigned()->nullable();
          $table->dateTime('f_marcacion')->nullable();

          $table->timestamps();

          $table->foreign('biometrico_id')
            ->references('id')
            ->on('rrhh_biometricos')
            ->onDelete('cascade');

          $table->foreign('persona_id')
            ->references('id')
            ->on('rrhh_personas')
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
        Schema::dropIfExists('rrhh_log_marcaciones');
    }
}
