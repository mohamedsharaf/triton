<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateI4NotiNotificacionesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('mysql')->create('noti_notificaciones', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('caso_id')->unsigned()->nullable();
            $table->integer('persona_id')->unsigned()->nullable();
            $table->integer('abogado_id')->unsigned()->nullable();
            $table->integer('actividad_solicitante_id')->unsigned()->nullable();
            $table->integer('actividad_notificacion_id')->unsigned()->nullable();
            $table->integer('funcionario_solicitante_id')->unsigned()->nullable();
            $table->integer('funcionario_notificador_id')->unsigned()->nullable();
            $table->integer('funcionario_entrega_id')->unsigned()->nullable();
            $table->integer('estado_notificacion_id')->unsigned()->nullable();

            $table->smallInteger('estado')->default('1')->unsigned();
            $table->string('codigo', 20)->nullable();

            $table->dateTime('solicitud_fh')->nullable();
            $table->string('solicitud_asunto', 500)->nullable();

            $table->string('persona_direccion', 200)->nullable();
            $table->string('persona_zona', 200)->nullable();
            $table->string('persona_municipio', 200)->nullable();
            $table->string('persona_telefono', 50)->nullable();
            $table->string('persona_celular', 50)->nullable();
            $table->string('persona_email', 100)->nullable();

            $table->string('abogado_direccion', 200)->nullable();
            $table->string('abogado_zona', 200)->nullable();
            $table->string('abogado_municipio', 200)->nullable();
            $table->string('abogado_telefono', 50)->nullable();
            $table->string('abogado_celular', 50)->nullable();
            $table->string('abogado_email', 100)->nullable();

            $table->smallInteger('notificacion_estado')->default('1')->unsigned();
            $table->dateTime('notificacion_fh')->nullable();
            $table->string('notificacion_observacion', 200)->nullable();
            $table->binary('notificacion_documento')->nullable();
            $table->string('notificacion_testigo_nombre', 200)->nullable();
            $table->string('notificacion_testigo_n_documento', 20)->nullable();

            $table->timestamps();

            $table->foreign('caso_id')
                ->references('id')
                ->on('Caso')
                ->onDelete('cascade');

            $table->foreign('persona_id')
                ->references('id')
                ->on('Persona')
                ->onDelete('cascade');

            $table->foreign('abogado_id')
                ->references('id')
                ->on('Abogado')
                ->onDelete('cascade');

            $table->foreign('actividad_solicitante_id')
                ->references('id')
                ->on('Actividad')
                ->onDelete('cascade');

            $table->foreign('actividad_notificacion_id')
                ->references('id')
                ->on('Actividad')
                ->onDelete('cascade');

            $table->foreign('funcionario_solicitante_id')
                ->references('id')
                ->on('Funcionario')
                ->onDelete('cascade');

            $table->foreign('funcionario_notificador_id')
                ->references('id')
                ->on('Funcionario')
                ->onDelete('cascade');

            $table->foreign('funcionario_entrega_id')
                ->references('id')
                ->on('Funcionario')
                ->onDelete('cascade');

            $table->foreign('estado_notificacion_id')
                ->references('id')
                ->on('EstadoNotificacion')
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
        Schema::connection('mysql')->dropIfExists('noti_notificaciones');
    }
}
