<?php

namespace App\Models\I4;

use Illuminate\Database\Eloquent\Model;

class Notificacion extends Model
{
    protected $connection = 'mysql';
    protected $table      = 'Notificacion';

    protected $fillable = [
        "version",
        "Notificacion",
        "FechaEmision",
        "FechaActividad",
        "Asunto",
        "NombreCompletoEmisor",
        "InstitucionEmisor",
        "OficinaEmisor",
        "CargoEmisor",
        "DireccionEmisor",
        "MunicipioEmisor",
        "TelefonosEmisor",
        "NombreCompletoNotificado",
        "NombreCompletoRepresentanteNotificado",
        "InstitucionNotificado",
        "OficinaNotificado",
        "CargoNotificado",
        "DireccionNotificado",
        "MunicipioNotificado",
        "TelefonosNotificado",
        "EmailNotificado",
        "DocumentoNotificacion",
        "_DocumentoNotificacion",
        "CreatorUser",
        "CreatorFullName",
        "CreationDate",
        "CreationIP",
        "UpdaterUser",
        "UpdaterFullName",
        "UpdaterDate",
        "UpdaterIP",
        "FechaProgramacion",
        "FechaEntregaProgramada",
        "NotasProgramacion",
        "Caso",
        "EstadoNotificacion",
        "PrioridadNotificacion",
        "ActividadEmision",
        "Actividad",
        "FuncionarioEmisor",
        "FuncionarioNotificado",
        "FuncionarioEntrega",
        "Abogado"
    ];

    protected $guarded  = [];
}