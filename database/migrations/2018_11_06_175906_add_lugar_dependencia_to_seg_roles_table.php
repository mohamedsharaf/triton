<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddLugarDependenciaToSegRolesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('seg_roles', function (Blueprint $table) {
            $table->text('lugar_dependencia')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('seg_roles', function (Blueprint $table) {
            $table->dropColumn('lugar_dependencia');
        });
    }
}
