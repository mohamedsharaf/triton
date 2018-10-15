<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePvtSolicitudesDelitosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pvt_solicitudes_delitos', function (Blueprint $table) {
            $table->increments('id');

            $table->integer('solicitud_id')->unsigned()->nullable();
            $table->integer('delito_id')->unsigned()->nullable();

            $table->smallInteger('estado')->default('1')->unsigned();
            $table->smallInteger('tentativa')->default('1')->unsigned();

            $table->timestamps();

            $table->foreign('solicitud_id')
                ->references('id')
                ->on('pvt_solicitudes')
                ->onDelete('cascade');

            $table->foreign('delito_id')
                ->references('id')
                ->on('pvt_delitos')
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
        Schema::dropIfExists('pvt_solicitudes_delitos');
    }
}
