<?php

namespace App\Models\Rrhh;

use Illuminate\Database\Eloquent\Model;

class RrhhAsistencia extends Model
{
    protected $table = 'rrhh_asistencias';

    protected $fillable = [
        'persona_id',

        'persona_id_rrhh_h1_i',
        'persona_id_rrhh_h1_s',
        'persona_id_rrhh_h2_i',
        'persona_id_rrhh_h2_s',

        'cargo_id',
        'unidad_desconcentrada_id',

        'log_marcaciones_id_i1',
        'log_marcaciones_id_s1',
        'log_marcaciones_id_i2',
        'log_marcaciones_id_s2',

        'horario_id_1',
        'horario_id_2',

        'salida_id_i1',
        'salida_id_s1',
        'salida_id_i2',
        'salida_id_s2',

        'fthc_id_h1',
        'fthc_id_h2',

        'h1_descuento',
        'h2_descuento',

        'estado',
        'fecha',

        'h1_i_omitir',
        'h1_s_omitir',
        'h2_i_omitir',
        'h2_s_omitir',

        'h1_min_retrasos',
        'h2_min_retrasos',

        'h1_i_omision_registro',
        'h1_s_omision_registro',
        'h2_i_omision_registro',
        'h2_s_omision_registro',

        'f_omision_registro',
        'e_omision_registro',

        'h1_falta',
        'h2_falta',

        'observaciones',
        'justificacion',

        'horario_1_i',
        'horario_1_s',

        'horario_2_i',
        'horario_2_s'
    ];

    protected $guarded  = [];
}