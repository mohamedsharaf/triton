<?php

namespace App\Models\Dpvt;

use Illuminate\Database\Eloquent\Model;

class PvtSolicitudComplementaria extends Model
{
    protected $table = 'pvt_solicitudes_complementarias';

    protected $fillable = [
        'solicitud_id',

        'complementario_dirigido_a',
        'complementario_trabajo_solicitado',
        'complementario_estado_pdf',
        'complementario_archivo_pdf'
    ];

    protected $guarded  = [];
}