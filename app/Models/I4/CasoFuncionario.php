<?php

namespace App\Models\I4;

use Illuminate\Database\Eloquent\Model;

class CasoFuncionario extends Model
{
    protected $connection = 'mysql';
    protected $table      = 'CasoFuncionario';

    protected $fillable = [
        "version",
        "FechaAlta",
        "FechaBaja",
        "Notas",
        "CreatorUser",
        "CreatorFullName",
        "CreationDate",
        "CreationIP",
        "UpdaterUser",
        "UpdaterFullName",
        "UpdaterDate",
        "UpdaterIP",
        "Caso",
        "DivisionFis",
        "DivisionPol",
        "Funcionario",
        "TipoAsignacion"
    ];

    protected $guarded  = [];
}