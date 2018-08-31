<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePvtDelitosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pvt_delitos', function (Blueprint $table) {
            $table->increments('id');

            $table->smallInteger('estado')->default('1')->unsigned();

            $table->string('codigo', 6)->nullable();
            $table->string('libro', 100)->nullable();
            $table->integer('n_libro')->unsigned()->nullable();
            $table->integer('n_titulo')->unsigned()->nullable();
            $table->integer('n_capitulo')->unsigned()->nullable();
            $table->integer('n_delito')->unsigned()->nullable();
            $table->integer('n_articulo')->unsigned()->nullable();
            $table->string('inciso', 100)->nullable();
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
        Schema::dropIfExists('pvt_delitos');
    }
}
