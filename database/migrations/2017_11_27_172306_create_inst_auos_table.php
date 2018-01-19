<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateInstAuosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('inst_auos', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('lugar_dependencia_id')->unsigned()->nullable();
            $table->integer('auo_id')->unsigned()->nullable();

            $table->smallInteger('estado')->default('1')->unsigned();
            $table->string('nombre', 250)->nullable();

            $table->timestamps();

            $table->foreign('lugar_dependencia_id')
                ->references('id')
                ->on('inst_lugares_dependencia')
                ->onDelete('cascade');

            $table->foreign('auo_id')
                ->references('id')
                ->on('inst_auos')
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
        Schema::dropIfExists('inst_auos');
    }
}
