<?php

namespace App\Models\I4;

use Illuminate\Database\Eloquent\Model;

class Caso extends Model
{
    protected $connection = 'mysql';
    protected $table      = 'Caso';

    protected $fillable = [
        "version",
        "Caso",
        "CodCasoJuz",
        "Titulo",
        "VencimientoCaso",
        "NivelReserva",
        "FechaHecho",
        "HoraHecho",
        "FechaDenuncia",
        "Dir",
        "Zona",
        "BreveDescripcionHecho",
        "CreatorUser",
        "CreatorFullName",
        "CreationDate",
        "CreationIP",
        "UpdaterUser",
        "UpdaterFullName",
        "UpdaterDate",
        "UpdaterIP",
        "Flagrancia",
        "Ley348",
        "MontoDanioEconomico",
        "Analisis",
        "Observaciones",
        "PuntosMapa",
        "EstadoCaso",
        "EtapaCaso",
        "OrigenCaso",
        "MedioDenuncia",
        "CalHecho",
        "CalDenuncia",
        "Muni",
        "EvaluacionAnalisis",
        "CategoriaHecho",
        "MotivoHecho",
        "ModusOperandi",
        "DivisionPol",
        "DivisionFis",
        "DivisionJuz",
        "DelitoPrincipal",
        "Denunciados",
        "Denunciantes",
        "Victimas",
        "Testigos",
        "Imputaciones",
        "Rechazos",
        "AcusacionesAbreviado",
        "AcusacionesJuicio",
        "Sobreseimientos",
        "CriteriosOportunidad",
        "SalidasAlternativas",
        "AudienciasPreparatoria",
        "AudienciasAbreviado",
        "AudienciasJuicio",
        "Condenas",
        "Absoluciones",
        "AllanamientosPositivos",
        "AllanamientosNegativos",
        "RequisasPositivas",
        "RequisasNegativas",
        "Requerimientos",
        "Actas",
        "Generales",
        "FechaInicio",
        "DuracionPreliminar",
        "DuracionPreparatoria",
        "DuracionJuicio",
        "DuracionAbreviado",
        "Tentativa"
    ];

    protected $guarded  = [];
}