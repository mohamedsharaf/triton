<?php

namespace App\Models\Rrhh;

use Illuminate\Database\Eloquent\Model;

class RrhhFuncionario extends Model
{
    protected $table = 'rrhh_funcionarios';

    protected $fillable = [
        'persona_id',
        'cargo_id',
        'unidad_desconcentrada_id',
        'estado',
        'situacion',
        'f_ingreso',
        'sueldo'
    ];

    protected $guarded  = [];
}