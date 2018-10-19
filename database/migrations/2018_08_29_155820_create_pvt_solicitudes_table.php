<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePvtSolicitudesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pvt_solicitudes', function (Blueprint $table) {
            $table->increments('id');

            $table->integer('municipio_id')->unsigned()->nullable();

            $table->smallInteger('estado')->default('1')->unsigned();
            $table->smallInteger('cerrado_abierto')->default('1')->unsigned();
            $table->integer('gestion')->unsigned()->nullable();
            $table->string('codigo', 10)->nullable();

            $table->smallInteger('solicitante')->unsigned()->nullable();
            $table->string('nombre_solicitante', 1000)->nullable();
            $table->text('delitos')->nullable();
            $table->text('recalificacion_delitos')->nullable();
            $table->string('n_caso', 50)->nullable();
            $table->string('denunciante', 500)->nullable();
            $table->string('denunciado', 500)->nullable();
            $table->string('victima', 500)->nullable();
            $table->string('persona_protegida', 500)->nullable();
            $table->smallInteger('etapa_proceso')->unsigned()->nullable();
            $table->date('f_solicitud')->nullable();
            $table->smallInteger('solicitud_estado_pdf')->default('1')->unsigned();
            $table->string('solicitud_documento_pdf', 100)->nullable();

            $table->string('usuario_tipo', 50)->nullable();
            $table->string('usuario_tipo_descripcion', 1000)->nullable();
            $table->string('usuario_nombre', 500)->nullable();
            $table->smallInteger('usuario_sexo')->unsigned()->nullable();
            $table->smallInteger('usuario_edad')->unsigned()->nullable();
            $table->string('usuario_celular', 100)->nullable();
            $table->string('usuario_domicilio', 500)->nullable();
            $table->string('usuario_otra_referencia', 500)->nullable();

            $table->string('dirigido_a_psicologia', 50)->nullable();
            $table->string('dirigido_psicologia', 50)->nullable();
            $table->smallInteger('dirigido_psicologia_estado_pdf')->default('1')->unsigned();
            $table->string('dirigido_psicologia_archivo_pdf', 100)->nullable();

            $table->string('dirigido_a_trabajo_social', 50)->nullable();
            $table->string('dirigido_trabajo_social', 50)->nullable();
            $table->smallInteger('dirigido_trabajo_social_estado_pdf')->default('1')->unsigned();
            $table->string('dirigido_trabajo_social_archivo_pdf', 100)->nullable();

            $table->string('dirigido_a_otro_trabajo', 50)->nullable();
            $table->string('dirigido_otro_trabajo', 1000)->nullable();
            $table->smallInteger('dirigido_otro_trabajo_estado_pdf')->default('1')->unsigned();
            $table->string('dirigido_otro_trabajo_archivo_pdf', 100)->nullable();

            $table->date('plazo_fecha_solicitud')->nullable();

            $table->date('plazo_psicologico_fecha_entrega_digital')->nullable();
            $table->smallInteger('plazo_psicologico_estado_pdf')->default('1')->unsigned();
            $table->string('plazo_psicologico_archivo_pdf', 100)->nullable();

            $table->date('plazo_social_fecha_entrega_digital')->nullable();
            $table->smallInteger('plazo_social_estado_pdf')->default('1')->unsigned();
            $table->string('plazo_social_archivo_pdf', 100)->nullable();

            $table->date('plazo_complementario_fecha')->nullable();
            $table->smallInteger('plazo_complementario_estado_pdf')->default('1')->unsigned();
            $table->string('plazo_complementario_archivo_pdf', 100)->nullable();

            $table->timestamps();

            $table->foreign('municipio_id')
                ->references('id')
                ->on('ubge_municipios')
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
        Schema::dropIfExists('pvt_solicitudes');
    }
}
