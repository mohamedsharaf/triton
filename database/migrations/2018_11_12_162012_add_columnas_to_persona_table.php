<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnasToPersonaTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('mysql')->table('Persona', function (Blueprint $table) {
            $table->smallInteger('triton_modificado')->default('1')->unsigned(); // 1=>NO; 2=>SI
            $table->integer('recinto_carcelario_id')->unsigned()->nullable();
            $table->smallInteger('dp_estado')->default('1')->unsigned(); // 1=>SIN DETENCION PREVENTIVA; 2=>CON DETENCION PREVENTIVA; 3=>X
            $table->smallInteger('dp_semaforo')->default('1')->unsigned(); // 1=>VERDE; 2=>AMARILLO; 3=>ROJO
            $table->smallInteger('dp_semaforo_delito')->default('1')->unsigned(); // 1=>VERDE; 2=>AMARILLO; 3=>ROJO
            $table->date('dp_fecha_detencion_preventiva')->nullable();
            $table->date('dp_fecha_conclusion_detencion')->nullable();
            $table->smallInteger('dp_etapa_gestacion_estado')->default('1')->unsigned(); // 1=>NO; 2=>SI
            $table->smallInteger('dp_etapa_gestacion_semana')->unsigned()->nullable(); // Calculado en semanas
            $table->smallInteger('dp_enfermo_terminal_estado')->default('1')->unsigned(); // 1=>NO; 2=>SI
            $table->string('dp_enfermo_terminal_tipo', 500)->nullable(); // Descripción del Tipo de Enfermedad
            $table->smallInteger('dp_persona_mayor_65')->default('1')->unsigned(); // 1=>NO; 2=>SI
            $table->smallInteger('dp_madre_lactante_1')->default('1')->unsigned(); // 1=>NO; 2=>SI
            $table->date('dp_madre_lactante_1_fecha_nacimiento_menor')->nullable();
            $table->smallInteger('dp_custodia_menor_6')->default('1')->unsigned(); // 1=>NO; 2=>SI
            $table->date('dp_custodia_menor_6_fecha_nacimiento_menor')->nullable();

            $table->smallInteger('dp_mayor_3')->default('1')->unsigned(); // 1=>NO; 2=>SI a semaforo rojo
            $table->smallInteger('dp_minimo_previsto_delito')->default('1')->unsigned(); // 1=>NO; 2=>SI a semaforo rojo
            // $table->smallInteger('dp_pena_menor_4')->default('1')->unsigned(); // 1=>NO; 2=>SI a semaforo rojo
            $table->smallInteger('dp_delito_pena_menor_4')->default('1')->unsigned(); // 1=>NO; 2=>SI a semaforo amarillo
            $table->smallInteger('dp_delito_patrimonial_menor_6')->default('1')->unsigned(); // 1=>NO; 2=>SI a semaforo amarillo
            $table->smallInteger('dp_etapa_preparatoria_dias_transcurridos_estado')->default('1')->unsigned(); // 1=>NO; 2=>SI
            $table->integer('dp_etapa_preparatoria_dias_transcurridos_numero')->unsigned()->nullable(); // mayor a 5 meses semaforo amarillo, mayor a 6 meses semaforo rojo

            $table->date('created_at')->nullable();
            $table->date('update_at')->nullable();
            $table->smallInteger('estado_segip')->default('1')->unsigned();
            $table->smallInteger('reincidencia')->default('1')->unsigned();

            $table->date('se_fecha_inicio_sentencia')->nullable();
            $table->string('se_tiempo_sentencia', 500)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::connection('mysql')->table('Persona', function (Blueprint $table) {
            $table->dropColumn('triton_modificado');
            $table->dropColumn('recinto_carcelario_id');
            $table->dropColumn('dp_estado');
            $table->dropColumn('dp_semaforo');
            $table->dropColumn('dp_semaforo_delito');
            $table->dropColumn('dp_fecha_detencion_preventiva');
            $table->dropColumn('dp_fecha_conclusion_detencion');
            $table->dropColumn('dp_etapa_gestacion_estado');
            $table->dropColumn('dp_etapa_gestacion_semana');
            $table->dropColumn('dp_enfermo_terminal_estado');
            $table->dropColumn('dp_enfermo_terminal_tipo');
            $table->dropColumn('dp_persona_mayor_65');
            $table->dropColumn('dp_madre_lactante_1');
            $table->dropColumn('dp_madre_lactante_1_fecha_nacimiento_menor');
            $table->dropColumn('dp_custodia_menor_6');
            $table->dropColumn('dp_custodia_menor_6_fecha_nacimiento_menor');
            $table->dropColumn('dp_mayor_3');
            $table->dropColumn('dp_minimo_previsto_delito');
            // $table->dropColumn('dp_pena_menor_4');
            $table->dropColumn('dp_delito_pena_menor_4');
            $table->dropColumn('dp_delito_patrimonial_menor_6');
            $table->dropColumn('dp_etapa_preparatoria_dias_transcurridos_estado');
            $table->dropColumn('dp_etapa_preparatoria_dias_transcurridos_numero');
            $table->dropColumn('created_at');
            $table->dropColumn('update_at');
            $table->dropColumn('estado_segip');
            $table->dropColumn('reincidencia');
        });
    }
}
