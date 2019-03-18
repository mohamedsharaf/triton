<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePvtDerivacionesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pvt_derivaciones', function (Blueprint $table) {
            $table->increments('id');
            $table->smallInteger('estado')->default('1')->unsigned();
            $table->string('motivo');
            $table->text('relato');
            $table->date('fecha')->nullable();
            $table->integer('institucion_id')->unsigned()->nullable();
            $table->integer('visitante_id')->unsigned()->nullable();
            $table->timestamps();

            $table->foreign('institucion_id')
                ->references('id')
                ->on('inst_instituciones')
                ->onDelete('cascade');

            $table->foreign('visitante_id')
                ->references('id')
                ->on('rrhh_visitantes')
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
        Schema::dropIfExists('pvt_derivaciones');
    }
}
