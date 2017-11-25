<?php

namespace App\Models\Rrhh;

use Illuminate\Database\Eloquent\Model;

class RrhhLogAlerta extends Model
{
    protected $table = 'rrhh_log_alertas';

    protected $fillable = [
        'biometrico_id',
        'tipo_emisor',
        'f_alerta',
        'mensaje'
    ];

    protected $guarded = [];
}