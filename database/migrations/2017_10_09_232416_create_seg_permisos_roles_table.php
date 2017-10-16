<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSegPermisosRolesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('seg_permisos_roles', function (Blueprint $table) {
          $table->increments('id');
          $table->integer('permiso_id')->unsigned();
          $table->integer('rol_id')->unsigned();

          $table->timestamps();

          $table->foreign('permiso_id')
            ->references('id')
            ->on('seg_permisos')
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
        Schema::dropIfExists('seg_permisos_roles');
    }

}
