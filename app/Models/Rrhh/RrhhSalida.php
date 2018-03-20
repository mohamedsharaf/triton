<?php

namespace App\Models\Rrhh;

use Illuminate\Database\Eloquent\Model;

class RrhhSalida extends Model
{
    protected $table = 'rrhh_salidas';

    protected $fillable = [
        'persona_id',
        'tipo_salida_id',
        'persona_id_superior',
        'persona_id_rrhh',

        'cargo_id',
        'unidad_desconcentrada_id',

        'estado',
        'codigo',
        'destino',
        'motivo',
        'f_salida',
        'f_retorno',
        'h_salida',
        'h_retorno',

        'n_horas',
        'con_sin_retorno',

        'n_dias',
        'periodo_salida',
        'periodo_retorno',

        'validar_superior',
        'f_validar_superior',

        'validar_rrhh',
        'f_validar_rrhh',

        'pdf',
        'papeleta_pdf',

        'log_marcaciones_id_s',
        'log_marcaciones_id_r',

        'salida_s',
        'salida_r',
        'min_retrasos'
    ];

    protected $guarded  = [];
}