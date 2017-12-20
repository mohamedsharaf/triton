<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRrhhFuncionariosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('rrhh_funcionarios', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('persona_id')->unsigned()->nullable();
            $table->integer('cargo_id')->unsigned()->nullable();
            $table->integer('unidad_desconcentrada_id')->unsigned()->nullable();
            $table->integer('horario_id_1')->unsigned()->nullable();
            $table->integer('horario_id_2')->unsigned()->nullable();

            $table->smallInteger('estado')->default('1')->unsigned();
            $table->smallInteger('situacion')->default('1')->unsigned();
            $table->smallInteger('documento_sw')->default('1')->unsigned();
            $table->date('f_ingreso')->nullable();
            $table->date('f_salida')->nullable();
            $table->double('sueldo')->nullable();
            $table->string('observaciones', 500)->nullable();
            $table->string('documento_file', 250)->nullable();

            $table->timestamps();

            $table->foreign('persona_id')
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

            $table->foreign('horario_id_1')
                ->references('id')
                ->on('rrhh_horarios')
                ->onDelete('cascade');

            $table->foreign('horario_id_2')
                ->references('id')
                ->on('rrhh_horarios')
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
        Schema::dropIfExists('rrhh_funcionarios');
    }
}
