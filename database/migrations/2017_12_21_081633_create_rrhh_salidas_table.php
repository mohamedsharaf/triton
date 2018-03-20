<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRrhhSalidasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('rrhh_salidas', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('persona_id')->unsigned()->nullable();
            $table->integer('tipo_salida_id')->unsigned()->nullable();
            $table->integer('persona_id_superior')->unsigned()->nullable();
            $table->integer('persona_id_rrhh')->unsigned()->nullable();

            $table->integer('cargo_id')->unsigned()->nullable();
            $table->integer('unidad_desconcentrada_id')->unsigned()->nullable();

            $table->smallInteger('estado')->default('1')->unsigned();
            $table->string('codigo', 12)->nullable();
            $table->string('destino', 500)->nullable();
            $table->string('motivo', 500)->nullable();
            $table->date('f_salida')->nullable();
            $table->date('f_retorno')->nullable();
            $table->time('h_salida')->nullable();
            $table->time('h_retorno')->nullable();

            $table->double('n_horas')->nullable();
            $table->smallInteger('con_sin_retorno')->unsigned()->nullable();

            $table->double('n_dias')->nullable();
            $table->smallInteger('periodo_salida')->unsigned()->nullable();
            $table->smallInteger('periodo_retorno')->unsigned()->nullable();

            $table->smallInteger('validar_superior')->default('1')->unsigned();
            $table->dateTime('f_validar_superior')->nullable();

            $table->smallInteger('validar_rrhh')->default('1')->unsigned();
            $table->dateTime('f_validar_rrhh')->nullable();

            $table->smallInteger('pdf')->default('1')->unsigned();
            $table->string('papeleta_pdf', 250)->nullable();

            $table->integer('log_marcaciones_id_s')->unsigned()->nullable();
            $table->integer('log_marcaciones_id_r')->unsigned()->nullable();

            $table->string('salida_s', 100)->default('NO MARCADO')->nullable();
            $table->string('salida_r', 100)->default('NO MARCADO')->nullable();
            $table->smallInteger('min_retrasos')->default('0')->unsigned();

            $table->timestamps();

            $table->foreign('persona_id')
                ->references('id')
                ->on('rrhh_personas')
                ->onDelete('cascade');

            $table->foreign('tipo_salida_id')
                ->references('id')
                ->on('rrhh_tipos_salida')
                ->onDelete('cascade');

            $table->foreign('persona_id_superior')
                ->references('id')
                ->on('rrhh_personas')
                ->onDelete('cascade');

            $table->foreign('persona_id_rrhh')
                ->references('id')
                ->on('rrhh_personas')
                ->onDelete('cascade');

            $table->foreign('cargo_id')
                ->references('id')
                ->on('inst_cargos')
                ->onDelete('cascade');

            $table->foreign('unidad_desconcentrada_id')
                ->references('id')
                ->on('inst_unidades_desconcentradas')
                ->onDelete('cascade');

            $table->foreign('log_marcaciones_id_s')
                ->references('id')
                ->on('rrhh_log_marcaciones')
                ->onDelete('cascade');

            $table->foreign('log_marcaciones_id_r')
                ->references('id')
                ->on('rrhh_log_marcaciones')
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
        Schema::dropIfExists('rrhh_salidas');
    }
}
