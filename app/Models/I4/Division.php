<?php

namespace App\Models\I4;

use Illuminate\Database\Eloquent\Model;

class Division extends Model
{
    protected $connection = 'mysql';
    protected $table      = 'Division';

    protected $fillable = [
        "version",
        "Division",
        "Activo",
        "RecibeDenuncia",
        "NumeracionCasoManual",
        "AnalizaDenuncia",
        "SolucionRapida",
        "ResuelveFlagrancia",
        "InvestigaDelito",
        "RecibeRepartoAutomatico",
        "EsArchivo",
        "Notas",
        "CreatorUser",
        "CreatorFullName",
        "CreationDate",
        "CreationIP",
        "UpdaterUser",
        "UpdaterFullName",
        "UpdaterDate",
        "UpdaterIP",
        "Oficina",
        "UltFuncionario",
        "DivisionAnalisis",
        "DivisionAnalisisFEVAP",
        "DivisionUSTPol",
        "DivisionUSTFis",
        "ResuelveMenores"
    ];

    protected $guarded  = [];
}