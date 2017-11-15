<?php

namespace App\Models\Rrhh;

use Illuminate\Database\Eloquent\Model;

class RrhhPersonaBiometrico extends Model
{
    protected $table = 'rrhh_personas_biometricos';

    protected $fillable = [
        'unidad_desconcentrada_id',
        'estado',
        'ip',
        'internal_id',
        'com_key',
        'soap_port',
        'udp_port',
        'encoding',
        'description',
        'e_conexion',
        'fs_conexion',
        'fb_conexion'
    ];

    protected $guarded  = [];
}