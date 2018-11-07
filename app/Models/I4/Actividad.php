<?php

namespace App\Models\I4;

use Illuminate\Database\Eloquent\Model;

class Actividad extends Model
{
    protected $connection = 'mysql';
    protected $table      = 'Actividad';

    protected $fillable = [
        "version",
        "Fecha",
        "Actividad",
        "Informe",
        "AllanamientoPositivo",
        "RequisaPositiva",
        "Documento",
        "_Documento",
        "AniosCondena",
        "MesesCondena",
        "DiasCondena",
        "MultaCondena",
        "CondenaSuspendida",
        "TotalAniosCondena",
        "TotalDiasCondena",
        "DiasCondenaCumplidosPreSentencia",
        "FechaLibertad",
        "FechaRevisionBeneficios",
        "FechaExtraMuro",
        "CreatorUser",
        "CreatorFullName",
        "CreationDate",
        "CreationIP",
        "UpdaterUser",
        "UpdaterFullName",
        "UpdaterDate",
        "UpdaterIP",
        "FechaAudiencia",
        "HoraIniProgramadaAudiencia",
        "HoraFinEstimadaAudiencia",
        "HoraIniAudiencia",
        "HoraFinAudiencia",
        "NotificacionesNoEntregadas",
        "DenunciadoNoPresente",
        "DefensorNoPresente",
        "FiscalNoPresente",
        "InvestigadorNoPresente",
        "SlimNoPresente",
        "JuradoNoPresente",
        "JuezNoPresente",
        "SolicitudDefensa",
        "SolicitudFiscal",
        "OtroMotivoSuspensionAudiencia",
        "DesicionesJuez",
        "Caso",
        "TipoActividad",
        "EstadoDocumento",
        "CalFecha",
        "Denunciado",
        "EstadoLibertad",
        "Asignado",
        "FechaIni",
        "FechaFin",
        "Instrucciones",
        "ActividadActualizaEstadoCaso"
    ];

    protected $guarded  = [];
}