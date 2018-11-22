<?php

namespace App\Models\Rrhh;

use Illuminate\Database\Eloquent\Model;

class RrhhPersona extends Model
{
    protected $table = 'rrhh_personas';

    protected $fillable = [
        'municipio_id_nacimiento',
        'municipio_id_residencia',
        'estado',
        'n_documento',
        'nombre',
        'ap_paterno',
        'ap_materno',
        'ap_esposo',
        'sexo',
        'f_nacimiento',
        'estado_civil',
        'domicilio',
        'telefono',
        'celular',
        'estado_segip',
        'certificacion_segip'
    ];

    protected $guarded  = [];
}
