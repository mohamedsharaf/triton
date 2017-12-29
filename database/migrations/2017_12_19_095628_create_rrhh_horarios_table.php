<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRrhhHorariosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('rrhh_horarios', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('lugar_dependencia_id')->unsigned()->nullable();

            $table->smallInteger('estado')->default('1')->unsigned();
            $table->smallInteger('defecto')->default('1')->unsigned();
            $table->smallInteger('tipo_horario')->default('1')->unsigned()->nullable();
            $table->string('nombre', 500)->nullable();
            $table->time('h_ingreso')->nullable();
            $table->time('h_salida')->nullable();
            $table->smallInteger('tolerancia')->default('0')->unsigned();
            $table->time('marcacion_ingreso_del')->nullable();
            $table->time('marcacion_ingreso_al')->nullable();
            $table->time('marcacion_salida_del')->nullable();
            $table->time('marcacion_salida_al')->nullable();
            $table->smallInteger('lunes')->default('1')->unsigned()->nullable();
            $table->smallInteger('martes')->default('1')->unsigned()->nullable();
            $table->smallInteger('miercoles')->default('1')->unsigned()->nullable();
            $table->smallInteger('jueves')->default('1')->unsigned()->nullable();
            $table->smallInteger('viernes')->default('1')->unsigned()->nullable();
            $table->smallInteger('sabado')->default('1')->unsigned()->nullable();
            $table->smallInteger('domingo')->default('1')->unsigned()->nullable();

            $table->timestamps();

            $table->foreign('lugar_dependencia_id')
                ->references('id')
                ->on('inst_lugares_dependencia')
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
        Schema::dropIfExists('rrhh_horarios');
    }
}
