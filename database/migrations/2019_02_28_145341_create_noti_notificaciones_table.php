<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateNotiNotificacionesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('noti_notificaciones', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('tipo_notificacion_id')->unsigned()->nullable();

            $table->smallInteger('estado')->default('1')->unsigned();
            $table->string('titulo', 50)->nullable();
            $table->string('contenido', 500)->nullable();
            $table->string('icono', 250)->nullable();


            $table->timestamps();

            $table->foreign('tipo_notificacion_id')
                ->references('id')
                ->on('noti_tipos_notificaciones')
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
        Schema::dropIfExists('noti_notificaciones');
    }
}
