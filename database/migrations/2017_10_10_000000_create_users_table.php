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

            $table->string('n_documento', 25)->unique()->nullable();
            $table->string('name');
            $table->string('ap_paterno', 50)->nullable();
            $table->string('ap_materno', 50)->nullable();
            $table->string('ap_esposo', 50)->nullable();
            $table->string('imagen', 250)->nullable();

            $table->string('email')->unique();
            $table->string('password');

            $table->rememberToken();
            $table->timestamps();

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
        Schema::dropIfExists('users');
    }
}
