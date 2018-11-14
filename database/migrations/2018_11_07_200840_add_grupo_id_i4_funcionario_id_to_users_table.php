<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddGrupoIdI4FuncionarioIdToUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->integer('grupo_id')->default('1')->unsigned()->nullable();
            $table->integer('i4_funcionario_id')->unsigned()->nullable();
            $table->smallInteger('i4_funcionario_id_estado')->default('1')->unsigned();

            $table->foreign('grupo_id')
                ->references('id')
                ->on('seg_grupos')
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
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('grupo_id');
            $table->dropColumn('i4_funcionario_id');
            $table->dropColumn('i4_funcionario_id_estado');
        });
    }
}
