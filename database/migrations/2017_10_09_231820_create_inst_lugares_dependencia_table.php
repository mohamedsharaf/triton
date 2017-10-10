<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateInstLugaresDependenciaTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('inst_lugares_dependencia', function (Blueprint $table) {
          $table->increments('id');
          $table->smallInteger('estado')->default('1')->unsigned();
          $table->string('nombre', 250);
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
        Schema::table('inst_lugares_dependencia', function (Blueprint $table) {
            //
        });
    }
}
