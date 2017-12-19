<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRrhhTiposSalidaTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('rrhh_tipos_salida', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('lugar_dependencia_id')->unsigned()->nullable();

            $table->smallInteger('estado')->default('1')->unsigned();
            $table->string('nombre', 500)->nullable();
            $table->smallInteger('tipo_salida')->default('1')->unsigned();
            $table->smallInteger('tipo_cronograma')->default('1')->unsigned();
            $table->double('hd_mes')->nullable();

            $table->timestamps();

            $table->foreign('lugar_dependencia_id')
                ->references('id')
                ->on('inst_lugares_dependencia')
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
        Schema::dropIfExists('rrhh_tipos_salida');
    }
}