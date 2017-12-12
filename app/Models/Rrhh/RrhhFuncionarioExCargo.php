<?php

namespace App\Models\Rrhh;

use Illuminate\Database\Eloquent\Model;

class RrhhFuncionarioExCargo extends Model
{
    protected $table = 'rrhh_funcionarios_ex_cargos';

    protected $fillable = [
        'persona_id',
        'cargo_id',
        'unidad_desconcentrada_id',
        'estado',
        'situacion',
        'documento_sw',
        'f_ingreso',
        'f_salida',
        'sueldo',
        'observaciones',
        'documento_file'
    ];

    protected $guarded  = [];
}