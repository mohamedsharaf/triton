<?php

namespace App\Models\Rrhh;

use Illuminate\Database\Eloquent\Model;

class RrhhBiometrico extends Model
{
    protected $table = 'rrhh_biometricos';

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
        'fb_conexion',
        'f_log_asistencia'
    ];

    protected $guarded  = [];
}