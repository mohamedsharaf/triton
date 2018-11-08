<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSegLdRolesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('seg_ld_roles', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('lugar_dependencia_id')->unsigned()->nullable();
            $table->integer('rol_id')->unsigned()->nullable();

            $table->timestamps();

            $table->foreign('lugar_dependencia_id')
                ->references('id')
                ->on('inst_lugares_dependencia')
                ->onDelete('cascade');

            $table->foreign('rol_id')
                ->references('id')
                ->on('seg_roles')
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
        Schema::dropIfExists('seg_ld_roles');
    }
}
