<?php

namespace App\Models\Rrhh;

use Illuminate\Database\Eloquent\Model;

class RrhhSalida extends Model
{
    protected $table = 'rrhh_salidas';

    protected $fillable = [
        'funcionario_id',
        'tipo_salida_id',
        'funcionario_id_superior',
        'estado',
        'codigo',
        'destino',
        'motivo',
        'f_salida',
        'f_retorno',
        'h_salida',
        'h_retorno',
        'con_sin_retorno',
        'n_dias',
        'periodo'
    ];

    protected $guarded  = [];
}