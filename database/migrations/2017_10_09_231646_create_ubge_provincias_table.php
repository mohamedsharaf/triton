<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUbgeProvinciasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('ubge_provincias', function (Blueprint $table) {
          $table->increments('id');
          $table->integer('departamento_id')->unsigned();
          $table->smallInteger('estado')->default('1')->unsigned();
          $table->string('codigo', 4)->unique();
          $table->string('nombre', 250);
          $table->timestamps();

          $table->foreign('departamento_id')
            ->references('id')
            ->on('departamentos')
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
        Schema::table('ubge_provincias', function (Blueprint $table) {
            //
        });
    }
}
