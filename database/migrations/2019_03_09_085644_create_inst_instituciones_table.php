<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateInstInstitucionesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('inst_instituciones', function (Blueprint $table) {
            $table->increments('id');
            $table->smallInteger('estado')->default('1')->unsigned();
            $table->string('nombre');
            $table->string('zona', 150)->nullable();
            $table->string('direccion', 150)->nullable();
            $table->string('telefono', 20)->nullable();
            $table->string('celular', 10)->nullable();
            $table->string('email')->unique();

            $table->integer('institucion_id')->unsigned()->nullable();
            $table->integer('ubge_municipios_id')->unsigned()->nullable();

            $table->timestamps();

            $table->foreign('ubge_municipios_id')
                ->references('id')
                ->on('ubge_municipios')
                ->onDelete('cascade');
            
            $table->foreign('institucion_id')
                ->references('id')
                ->on('inst_instituciones')
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
        Schema::dropIfExists('inst_instituciones');
    }
}
