<?php

namespace App\Models\Rrhh;

use Illuminate\Database\Eloquent\Model;

class RrhhLogMarcacion extends Model
{
    protected $table = 'rrhh_log_marcaciones';

    protected $fillable = [
        'biometrico_id',
        'persona_id',
        'tipo_marcacion',
        'n_documento_biometrico',
        'f_marcacion'
    ];

    protected $guarded  = [];
}