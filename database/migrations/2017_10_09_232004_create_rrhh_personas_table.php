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
        Schema::table('rrhh_personas', function (Blueprint $table) {
          $table->increments('id');
          $table->smallInteger('estado')->default('1')->unsigned();
          $table->integer('n_documento')->unique()->unsigned();
          $table->string('nombre', 250);
          $table->smallInteger('privilegio')->unsigned();
          $table->integer('password')->unsigned();
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
        Schema::table('rrhh_personas', function (Blueprint $table) {
            //
        });
    }
}
