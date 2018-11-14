<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRecintosCarcelariosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('mysql')->create('RecintosCarcelarios', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('Muni_id')->unsigned();

            $table->smallInteger('estado')->default('1')->unsigned();
            $table->smallInteger('tipo_recinto')->default('1')->unsigned(); // 1=> Recinto Penitenciario; 2=>Carceleta
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
        Schema::connection('mysql')->dropIfExists('RecintosCarcelarios');
    }
}
