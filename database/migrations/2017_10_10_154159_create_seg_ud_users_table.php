<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSegUdUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('seg_ud_users', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('unidad_desconcentrada_id')->unsigned()->nullable();
            $table->integer('user_id')->unsigned()->nullable();

            $table->timestamps();

            $table->foreign('unidad_desconcentrada_id')
              ->references('id')
              ->on('inst_unidades_desconcentradas')
              ->onDelete('cascade');

            $table->foreign('user_id')
              ->references('id')
              ->on('users')
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
        Schema::dropIfExists('seg_ud_users');
    }
}
