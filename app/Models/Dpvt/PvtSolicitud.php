<?php

namespace App\Models\Dpvt;

use Illuminate\Database\Eloquent\Model;

class PvtSolicitud extends Model
{
    protected $table = 'pvt_solicitudes';

    protected $fillable = [
        'persona_id_solicitante',
        'municipio_id',

        'estado',
        'gestion',
        'codigo',

        'solicitante',
        'delitos',
        'recalificacion_delitos',
        'n_caso',
        'denunciante',
        'denunciado',
        'victima',
        'persona_protegida',
        'etapa_proceso',
        'f_solicitud',
        'solicitud_estado_pdf',
        'solicitud_documento_pdf',

        'usuario_tipo',
        'usuario_tipo_descripcion',
        'usuario_nombre',
        'usuario_sexo',
        'usuario_edad',
        'usuario_celular',
        'usuario_domicilio',
        'usuario_otra_referencia',

        'dirigido_a',
        'dirigido_psicologia',
        'dirigido_trabajo_social',
        'dirigido_otro_trabajo',

        'plazo_fecha_solicitud',
        'plazo_psicologico_fecha',
        'plazo_psicologico_estado_pdf',
        'plazo_psicologico_archivo_pdf',
        'plazo_social_fecha',
        'plazo_social_estado_pdf',
        'plazo_social_archivo_pdf',
        'plazo_complementario_fecha',
        'plazo_complementario_estado_pdf',
        'plazo_complementario_archivo_pdf',
        'plazo_fecha_recepcion',
        'plazo_fecha_entrega_digital',
        'plazo_fecha_entrega_fisico'
    ];

    protected $guarded  = [];
}