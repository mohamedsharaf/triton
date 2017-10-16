<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUbgeDepartamentosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ubge_departamentos', function (Blueprint $table) {
            $table->increments('id');

            $table->smallInteger('estado')->default('1')->unsigned();
            $table->string('codigo', 2)->unique()->nullable();
            $table->string('codigo_2', 2)->unique()->nullable();
            $table->string('nombre', 250)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('ubge_departamentos');
    }
}
