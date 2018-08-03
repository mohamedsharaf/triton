<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAsfiRetencionesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('asfi_retenciones', function (Blueprint $table) {
            $table->increments('id');

            $table->integer('persona_id')->unsigned()->nullable();
            $table->integer('lugar_dependencia_id')->unsigned()->nullable();
            $table->integer('auo_id')->unsigned()->nullable();
            $table->integer('cargo_id')->unsigned()->nullable();


            $table->timestamps();

            $table->foreign('persona_id')
                ->references('id')
                ->on('rrhh_personas')
                ->onDelete('cascade');

            $table->foreign('lugar_dependencia_id')
                ->references('id')
                ->on('inst_lugares_dependencia')
                ->onDelete('cascade');

            $table->foreign('auo_id')
                ->references('id')
                ->on('inst_auos')
                ->onDelete('cascade');

            $table->foreign('cargo_id')
                ->references('id')
                ->on('inst_cargos')
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
        Schema::dropIfExists('asfi_retenciones');
    }
}
