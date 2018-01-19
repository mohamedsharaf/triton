<?php

namespace App\Models\Rrhh;

use Illuminate\Database\Eloquent\Model;

class RrhhTipoSalida extends Model
{
    protected $table = 'rrhh_tipos_salida';

    protected $fillable = [
        'lugar_dependencia_id',
        'estado',
        'nombre',
        'tipo_cronograma',
        'tipo_salida',
        'horas_mes'
    ];

    protected $guarded  = [];
}