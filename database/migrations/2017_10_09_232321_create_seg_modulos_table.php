<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSegModulosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('seg_modulos', function (Blueprint $table) {
          $table->increments('id');

          $table->smallInteger('estado')->default('1')->unsigned();
          $table->string('codigo', 2)->unique()->nullable();
          $table->string('nombre', 500)->nullable();

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
        Schema::dropIfExists('seg_modulos');
    }
}
