<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePvtSolicitudesComplementariasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pvt_solicitudes_complementarias', function (Blueprint $table) {
            $table->increments('id');

            $table->integer('solicitud_id')->unsigned()->nullable();

            $table->smallInteger('estado')->default('1')->unsigned();

            $table->string('complementario_dirigido_a', 1000)->nullable();
            $table->string('complementario_trabajo_solicitado', 1000)->nullable();
            $table->smallInteger('complementario_estado_pdf')->default('1')->unsigned();
            $table->string('complementario_archivo_pdf', 100)->nullable();

            $table->timestamps();

            $table->foreign('solicitud_id')
                ->references('id')
                ->on('pvt_solicitudes')
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
        Schema::dropIfExists('pvt_solicitudes_complementarias');
    }
}
