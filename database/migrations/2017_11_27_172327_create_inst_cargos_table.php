<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateInstCargosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('inst_cargos', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('auo_id')->unsigned()->nullable();
            $table->integer('cargo_id')->unsigned()->nullable();
            $table->integer('tipo_cargo_id')->unsigned()->nullable();

            $table->smallInteger('estado')->default('1')->unsigned();
            $table->smallInteger('acefalia')->default('1')->unsigned();
            $table->string('item_contrato', 50)->nullable();
            $table->string('nombre', 250)->nullable();

            $table->timestamps();

            $table->foreign('auo_id')
                ->references('id')
                ->on('inst_auos')
                ->onDelete('cascade');

            $table->foreign('cargo_id')
                ->references('id')
                ->on('inst_cargos')
                ->onDelete('cascade');

            $table->foreign('tipo_cargo_id')
                ->references('id')
                ->on('inst_tipos_cargo')
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
        Schema::dropIfExists('inst_cargos');
    }
}
