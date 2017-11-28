<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRrhhFuncionariosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('rrhh_funcionarios', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('persona_id')->unsigned()->nullable();
            $table->integer('cargo_id')->unsigned()->nullable();

            $table->smallInteger('estado')->default('1')->unsigned();

            $table->foreign('persona_id')
                ->references('id')
                ->on('rrhh_personas')
                ->onDelete('cascade');

            $table->foreign('cargo_id')
                ->references('id')
                ->on('inst_cargos')
                ->onDelete('cascade');

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
        Schema::dropIfExists('rrhh_funcionarios');
    }
}
