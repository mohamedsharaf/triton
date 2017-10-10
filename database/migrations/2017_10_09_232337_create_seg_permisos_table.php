<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSegPermisosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('seg_permisos', function (Blueprint $table) {
          $table->increments('id');
          $table->integer('modulo_id')->unsigned();

          $table->smallInteger('estado')->default('1')->unsigned();
          $table->string('codigo', 4)->unique()->nullable();
          $table->string('nombre', 500)->nullable();

          $table->timestamps();

          $table->foreign('modulo_id')
            ->references('id')
            ->on('seg_modulos')
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
        Schema::dropIfExists('seg_permisos');
    }
}
