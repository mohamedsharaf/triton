<?php

namespace App\Models\I4;

use Illuminate\Database\Eloquent\Model;

class TipoActividad extends Model
{
    protected $connection = 'mysql';
    protected $table      = 'TipoActividad';

    protected $fillable = [
        "version",
        "TipoActividad",
        "Activo",
        "CreaEnPreliminar",
        "CreaEnPreparatoria",
        "CreaEnJuicio",
        "CreaEnAbreviado",
        "CreaEnEjecucionPenal",
        "CreaEnCerrado",
        "VencimientoGeneral",
        "VencimientoFlagrancia",
        "VencimientoLey348",
        "AlertaActividadAbierta",
        "DiasCierreAutomatico",
        "Notas",
        "CreatorUser",
        "CreatorFullName",
        "CreationDate",
        "CreationIP",
        "UpdaterUser",
        "UpdaterFullName",
        "UpdaterDate",
        "UpdaterIP",
        "FisVe",
        "FisCrea",
        "FisEdita",
        "FisElimina",
        "FisAnula",
        "PolVe",
        "PolCrea",
        "PolEdita",
        "PolElimina",
        "PolAnula",
        "JuzVe",
        "JuzCrea",
        "JuzEdita",
        "JuzElimina",
        "JuzAnula",
        "AbogadoVe",
        "RequiereDenunciado",
        "RequiereNombres",
        "RequiereNumDocID",
        "RequiereDomicilio",
        "Planifica",
        "Denunciados",
        "Notificaciones",
        "Allanamiento",
        "Requisa",
        "Audiencia",
        "Condena",
        "Libertad",
        "ClaseActividad",
        "EtapaCaso",
        "EstadoCaso",
        "EstadoPersona",
        "CreaEnApelaciones"
    ];

    protected $guarded  = [];
}