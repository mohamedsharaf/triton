<?php

namespace App\Models\I4;

use Illuminate\Database\Eloquent\Model;

class I4NotiNotificacion extends Model
{
    protected $connection = 'mysql';
    protected $table      = 'i4_noti_notificaciones';

    protected $fillable = [
        "caso_id",
        "persona_id",
        "abogado_id",
        "actividad_solicitante_id",
        "actividad_notificacion_id",
        "funcionario_solicitante_id",
        "funcionario_notificador_id",
        "funcionario_entrega_id",
        "estado_notificacion_id",
        "estado",
        "codigo",

        "solicitud_fh",
        "solicitud_asunto",

        "persona_estado",
        "persona_direccion",
        "persona_zona",
        "persona_municipio",
        "persona_telefono",
        "persona_celular",
        "persona_email",

        "abogado_direccion",
        "abogado_zona",
        "abogado_municipio",
        "abogado_telefono",
        "abogado_celular",
        "abogado_email",

        "notificacion_estado",
        "notificacion_fh",
        "notificacion_observacion",
        "notificacion_documento",
        "notificacion_testigo_nombre",
        "notificacion_testigo_n_documento"
    ];

    protected $guarded  = [];
}
