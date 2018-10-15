<?php

namespace App\Models\Dpvt;

use Illuminate\Database\Eloquent\Model;

class PvtResolucion extends Model
{
    protected $table = 'pvt_resoluciones';

    protected $fillable = [
        'solicitud_id',

        'estado',

        'fecha_inicio',
        'fecha_entrega_digital',
        'fecha_entrega_fisico',
        'informe_seguimiento_fecha',
        'informe_seguimiento_estado_pdf',
        'informe_seguimiento_archivo_pdf',
        'complementario_fecha',
        'complementario_estado_pdf',
        'complementario_archivo_pdf',

        'resolucion_descripcion',
        'resolucion_fecha_emision',
        'resolucion_estado_pdf',
        'resolucion_archivo_pdf',
        'resolucion_tipo_disposicion',
        'resolucion_medidas_proteccion',
        'resolucion_instituciones_coadyuvantes'
    ];

    protected $guarded  = [];
}