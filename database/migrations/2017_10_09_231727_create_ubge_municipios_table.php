<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUbgeMunicipiosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ubge_municipios', function (Blueprint $table) {
          $table->increments('id');
          $table->integer('provincia_id')->unsigned();

          $table->smallInteger('estado')->default('1')->unsigned();
          $table->string('codigo', 6)->unique()->nullable();
          $table->string('nombre', 250)->nullable();

          $table->foreign('provincia_id')
            ->references('id')
            ->on('ubge_provincias')
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
        Schema::dropIfExists('ubge_municipios');
    }
}
