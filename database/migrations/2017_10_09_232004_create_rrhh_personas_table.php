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

          $table->smallInteger('estado')->default('1')->unsigned();
          $table->integer('n_documento')->unique()->unsigned()->nullable();
          $table->string('nombre', 250)->nullable();
          $table->smallInteger('privilegio')->unsigned()->nullable();
          $table->integer('password')->unsigned()->nullable();

          $table->timestamps();
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
