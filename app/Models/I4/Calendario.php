<?php

namespace App\Models\I4;

use Illuminate\Database\Eloquent\Model;

class Calendario extends Model
{
    protected $connection = 'mysql';
    protected $table      = 'Calendario';

    protected $fillable = [
        "version",
        "Calendario",
        "FechaAtomica",
        "Anio",
        "Mes",
        "Dia",
        "NumDiaSemana",
        "NumDiaAnio",
        "NomDia",
        "NomMes",
        "Habil",
        "FeriadoNal",
        "FeriadoEnDep",
        "CreatorUser",
        "CreatorFullName",
        "CreatorDate",
        "CreatorIP",
        "UpdaterUser",
        "UpdaterFullName",
        "UpdaterDate",
        "UpdaterIP"
    ];

    protected $guarded  = [];
}