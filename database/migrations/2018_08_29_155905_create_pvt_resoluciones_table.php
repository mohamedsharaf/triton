<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePvtResolucionesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pvt_resoluciones', function (Blueprint $table) {
            $table->increments('id');

            $table->integer('solicitud_id')->unsigned()->nullable();

            $table->smallInteger('estado')->default('1')->unsigned();

            $table->string('resolucion_descripcion', 1000)->nullable();
            $table->date('resolucion_fecha_emision')->nullable();
            $table->smallInteger('resolucion_estado_pdf')->default('1')->unsigned();
            $table->string('resolucion_archivo_pdf', 100)->nullable();
            $table->string('resolucion_tipo_disposicion', 50)->nullable();
            $table->string('resolucion_medidas_proteccion', 50)->nullable();
            $table->string('resolucion_otra_medidas_proteccion', 1000)->nullable();
            $table->string('resolucion_instituciones_coadyuvantes', 1000)->nullable();
            $table->smallInteger('resolucion_estado_pdf_2')->default('1')->unsigned();
            $table->string('resolucion_archivo_pdf_2', 100)->nullable();

            $table->date('fecha_inicio')->nullable();
            $table->date('fecha_entrega_digital')->nullable();
            $table->date('informe_seguimiento_fecha')->nullable();
            $table->smallInteger('informe_seguimiento_estado_pdf')->default('1')->unsigned();
            $table->string('informe_seguimiento_archivo_pdf', 100)->nullable();
            $table->date('complementario_fecha')->nullable();
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
        Schema::dropIfExists('pvt_resoluciones');
    }
}
