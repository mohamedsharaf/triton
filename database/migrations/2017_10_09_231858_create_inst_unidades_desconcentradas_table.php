<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateInstUnidadesDesconcentradasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('inst_unidades_desconcentradas', function (Blueprint $table) {
          $table->increments('id');
          $table->integer('lugar_dependencia_id')->unsigned()->nullable();
          $table->integer('municipio_id')->unsigned()->nullable();

          $table->smallInteger('estado')->default('1')->unsigned();
          $table->string('nombre', 1000)->nullable();
          $table->string('direccion', 1000)->nullable();
          $table->timestamps();

          $table->foreign('lugar_dependencia_id')
            ->references('id')
            ->on('inst_lugares_dependencia')
            ->onDelete('cascade');

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
        Schema::dropIfExists('inst_unidades_desconcentradas');
    }
}
