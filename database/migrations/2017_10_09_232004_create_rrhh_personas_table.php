<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRrhhPersonasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('rrhh_personas', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('municipio_id_nacimiento')->unsigned()->nullable();
            $table->integer('municipio_id_residencia')->unsigned()->nullable();

            $table->smallInteger('estado')->default('1')->unsigned();
            $table->string('n_documento', 25)->unique()->nullable();
            $table->string('nombre', 50)->nullable();
            $table->string('ap_paterno', 50)->nullable();
            $table->string('ap_materno', 50)->nullable();
            $table->string('ap_esposo', 50)->nullable();
            $table->string('sexo', 1)->nullable();
            $table->date('f_nacimiento')->nullable();
            $table->smallInteger('estado_civil')->unsigned()->nullable();
            $table->string('domicilio', 500)->nullable();
            $table->string('telefono', 50)->nullable();
            $table->string('celular', 50)->nullable();

            $table->smallInteger('estado_segip')->default('1')->unsigned();

            $table->timestamps();

            $table->foreign('municipio_id_nacimiento')
                ->references('id')
                ->on('ubge_municipios')
                ->onDelete('cascade');

            $table->foreign('municipio_id_residencia')
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
        Schema::dropIfExists('rrhh_personas');
    }
}
