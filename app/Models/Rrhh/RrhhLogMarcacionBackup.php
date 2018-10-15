<?php

namespace App\Models\Rrhh;

use Illuminate\Database\Eloquent\Model;

class RrhhLogMarcacionBackup extends Model
{
    protected $table = 'rrhh_log_marcaciones_backup';

    protected $fillable = [
        'biometrico_id',
        'tipo_marcacion',
        'n_documento_biometrico',
        'f_marcacion',
        'estado'
    ];

    protected $guarded  = [];
}