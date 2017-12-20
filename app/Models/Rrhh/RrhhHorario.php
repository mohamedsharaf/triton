<?php

namespace App\Models\Rrhh;

use Illuminate\Database\Eloquent\Model;

class RrhhHorario extends Model
{
    protected $table = 'rrhh_horarios';

    protected $fillable = [
        'lugar_dependencia_id',
        'estado',
        'defecto',
        'tipo_horario',
        'nombre',
        'h_ingreso',
        'h_salida',
        'tolerancia',
        'marcacion_ingreso_del',
        'marcacion_ingreso_al',
        'marcacion_salida_del',
        'marcacion_salida_al',
        'lunes',
        'martes',
        'miercoles',
        'jueves',
        'viernes',
        'sabado',
        'domingo'
    ];

    protected $guarded  = [];
}