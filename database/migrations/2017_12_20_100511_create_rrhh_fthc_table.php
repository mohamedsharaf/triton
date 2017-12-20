<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRrhhFthcTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('rrhh_fthc', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('lugar_dependencia_id')->unsigned()->nullable();
            $table->integer('unidad_desconcentrada_id')->unsigned()->nullable();
            $table->integer('horario_id')->unsigned()->nullable();

            $table->smallInteger('estado')->default('1')->unsigned();
            $table->date('fecha')->nullable();
            $table->string('nombre', 500)->nullable();
            $table->smallInteger('tipo_fthc')->unsigned()->nullable();

            $table->smallInteger('tipo_horario')->unsigned()->nullable();
            $table->string('sexo', 1)->nullable();

            $table->timestamps();

            $table->foreign('lugar_dependencia_id')
                ->references('id')
                ->on('inst_lugares_dependencia')
                ->onDelete('cascade');

            $table->foreign('unidad_desconcentrada_id')
                ->references('id')
                ->on('inst_unidades_desconcentradas')
                ->onDelete('cascade');

            $table->foreign('horario_id')
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
        Schema::dropIfExists('rrhh_fthc');
    }
}
