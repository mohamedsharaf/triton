<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnasToCasoTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('mysql')->table('Caso', function (Blueprint $table) {
            $table->smallInteger('triton_modificado')->default('1')->unsigned(); // 1=>NO; 2=>SI
            $table->smallInteger('n_detenidos')->default('0')->unsigned(); // Cantidad de detenidos
            $table->dateTime('created_at')->default('now()')->unsigned();
            $table->dateTime('updated_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::connection('mysql')->table('Caso', function (Blueprint $table) {
            $table->dropColumn('triton_modificado');
            $table->dropColumn('n_detenidos');
            $table->dropColumn('created_at');
            $table->dropColumn('updated_at');
        });
    }
}
