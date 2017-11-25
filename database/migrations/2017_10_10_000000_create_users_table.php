<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Schema::defaultStringLength(191);
        Schema::create('users', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('rol_id')->unsigned()->nullable();
            $table->integer('persona_id')->unsigned()->nullable();

            $table->smallInteger('estado')->default('1')->unsigned();
            $table->string('name');

            $table->string('imagen', 250)->nullable();

            $table->string('email')->unique();
            $table->string('password');

            $table->text('lugar_dependencia')->nullable();

            $table->rememberToken();
            $table->timestamps();

            $table->foreign('rol_id')
              ->references('id')
              ->on('seg_roles')
              ->onDelete('cascade');

            $table->foreign('persona_id')
                ->references('id')
                ->on('rrhh_personas')
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
        Schema::dropIfExists('users');
    }
}
