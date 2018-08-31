<?php

namespace App\Models\Dpvt;

use Illuminate\Database\Eloquent\Model;

class PvtSolicitudDelito extends Model
{
    protected $table = 'pvt_solicitudes_delitos';

    protected $fillable = [
        'solicitud_id',
        'delito_id',

        'estado',
        'tentativa'
    ];

    protected $guarded  = [];
}