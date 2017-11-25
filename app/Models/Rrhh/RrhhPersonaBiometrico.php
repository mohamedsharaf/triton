<?php

namespace App\Models\Rrhh;

use Illuminate\Database\Eloquent\Model;

class RrhhPersonaBiometrico extends Model
{
    protected $table = 'rrhh_personas_biometricos';

    protected $fillable = [
        'persona_id',
        'biometrico_id',
        'estado',
        'f_registro_biometrico',
        'n_documento_biometrico',
        'nombre',
        'privilegio',
        'password'
    ];

    protected $guarded  = [];
}