<?php

namespace App\Models\Rrhh;

use Illuminate\Database\Eloquent\Model;

class RrhhFthc extends Model
{
    protected $table = 'rrhh_fthc';

    protected $fillable = [
        'lugar_dependencia_id',
        'unidad_desconcentrada_id',
        'horario_id',
        'estado',
        'tipo_fthc',
        'tipo_horario',
        'sexo',
        'f_nacimiento',
        'tolerancia',
        'nombre'
    ];

    protected $guarded  = [];
}