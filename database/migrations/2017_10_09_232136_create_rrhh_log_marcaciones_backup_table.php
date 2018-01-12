<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRrhhLogMarcacionesBackupTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('rrhh_log_marcaciones_backup', function (Blueprint $table) {
          $table->increments('id');
          $table->integer('biometrico_id')->unsigned();

          $table->smallInteger('estado')->default('1')->unsigned()->nullable();
          $table->smallInteger('tipo_marcacion')->default('1')->unsigned();
          $table->integer('n_documento_biometrico')->unsigned()->nullable();
          $table->dateTime('f_marcacion')->nullable();

          $table->timestamps();

          $table->foreign('biometrico_id')
            ->references('id')
            ->on('rrhh_biometricos')
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
        Schema::dropIfExists('rrhh_log_marcaciones_backup');
    }
}
