<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRrhhAsistenciasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('rrhh_asistencias', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('persona_id')->unsigned()->nullable();
            $table->integer('persona_id_rrhh_h1_i')->unsigned()->nullable();
            $table->integer('persona_id_rrhh_h1_s')->unsigned()->nullable();
            $table->integer('persona_id_rrhh_h2_i')->unsigned()->nullable();
            $table->integer('persona_id_rrhh_h2_s')->unsigned()->nullable();

            $table->integer('cargo_id')->unsigned()->nullable();
            $table->integer('unidad_desconcentrada_id')->unsigned()->nullable();

            $table->integer('log_marcaciones_id_i1')->unsigned()->nullable();
            $table->integer('log_marcaciones_id_s1')->unsigned()->nullable();
            $table->integer('log_marcaciones_id_i2')->unsigned()->nullable();
            $table->integer('log_marcaciones_id_s2')->unsigned()->nullable();

            $table->integer('horario_id_1')->unsigned()->nullable();
            $table->integer('horario_id_2')->unsigned()->nullable();

            $table->integer('salida_id_i1')->unsigned()->nullable();
            $table->integer('salida_id_s1')->unsigned()->nullable();
            $table->integer('salida_id_i2')->unsigned()->nullable();
            $table->integer('salida_id_s2')->unsigned()->nullable();

            $table->integer('fthc_id_h1')->unsigned()->nullable();
            $table->integer('fthc_id_h2')->unsigned()->nullable();

            $table->smallInteger('estado')->default('1')->unsigned()->nullable();
            $table->date('fecha')->nullable();

            $table->smallInteger('h1_i_omitir')->default('1')->unsigned()->nullable();
            $table->smallInteger('h1_s_omitir')->default('1')->unsigned()->nullable();
            $table->smallInteger('h2_i_omitir')->default('1')->unsigned()->nullable();
            $table->smallInteger('h2_s_omitir')->default('1')->unsigned()->nullable();

            $table->smallInteger('h1_min_retrasos')->default('0')->unsigned();
            $table->smallInteger('h2_min_retrasos')->default('0')->unsigned();

            $table->double('h1_descuento')->default('0')->unsigned();
            $table->double('h2_descuento')->default('0')->unsigned();

            $table->smallInteger('h1_i_omision_registro')->default('1')->unsigned();
            $table->smallInteger('h1_s_omision_registro')->default('1')->unsigned();
            $table->smallInteger('h2_i_omision_registro')->default('1')->unsigned();
            $table->smallInteger('h2_s_omision_registro')->default('1')->unsigned();

            $table->date('f_omision_registro')->nullable();
            $table->smallInteger('e_omision_registro')->default('1')->unsigned()->nullable();

            $table->smallInteger('h1_falta')->default('1')->unsigned();
            $table->smallInteger('h2_falta')->default('1')->unsigned();

            $table->string('observaciones', 500)->nullable();
            $table->string('justificacion', 500)->nullable();

            $table->string('horario_1_i', 100)->nullable();
            $table->string('horario_1_s', 100)->nullable();

            $table->string('horario_2_i', 100)->nullable();
            $table->string('horario_2_s', 100)->nullable();

            $table->timestamps();

            $table->foreign('persona_id')
                ->references('id')
                ->on('rrhh_personas')
                ->onDelete('cascade');

            $table->foreign('persona_id_rrhh_h1_i')
                ->references('id')
                ->on('rrhh_personas')
                ->onDelete('cascade');

            $table->foreign('persona_id_rrhh_h1_s')
                ->references('id')
                ->on('rrhh_personas')
                ->onDelete('cascade');

            $table->foreign('persona_id_rrhh_h2_i')
                ->references('id')
                ->on('rrhh_personas')
                ->onDelete('cascade');

            $table->foreign('persona_id_rrhh_h2_s')
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

            $table->foreign('log_marcaciones_id_i1')
                ->references('id')
                ->on('rrhh_log_marcaciones')
                ->onDelete('cascade');

            $table->foreign('log_marcaciones_id_s1')
                ->references('id')
                ->on('rrhh_log_marcaciones')
                ->onDelete('cascade');

            $table->foreign('log_marcaciones_id_i2')
                ->references('id')
                ->on('rrhh_log_marcaciones')
                ->onDelete('cascade');

            $table->foreign('log_marcaciones_id_s2')
                ->references('id')
                ->on('rrhh_log_marcaciones')
                ->onDelete('cascade');

            $table->foreign('horario_id_1')
                ->references('id')
                ->on('rrhh_horarios')
                ->onDelete('cascade');

            $table->foreign('horario_id_2')
                ->references('id')
                ->on('rrhh_horarios')
                ->onDelete('cascade');

            $table->foreign('salida_id_i1')
                ->references('id')
                ->on('rrhh_salidas')
                ->onDelete('cascade');

            $table->foreign('salida_id_s1')
                ->references('id')
                ->on('rrhh_salidas')
                ->onDelete('cascade');

            $table->foreign('salida_id_i2')
                ->references('id')
                ->on('rrhh_salidas')
                ->onDelete('cascade');

            $table->foreign('salida_id_s2')
                ->references('id')
                ->on('rrhh_salidas')
                ->onDelete('cascade');

            $table->foreign('fthc_id_h1')
                ->references('id')
                ->on('rrhh_fthc')
                ->onDelete('cascade');

            $table->foreign('fthc_id_h2')
                ->references('id')
                ->on('rrhh_fthc')
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
        Schema::dropIfExists('rrhh_asistencias');
    }
}
