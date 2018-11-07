<?php

namespace App\Models\I4;

use Illuminate\Database\Eloquent\Model;

class Delito extends Model
{
    protected $connection = 'mysql';
    protected $table      = 'Delito';

    protected $fillable = [
        "version",
        "Libro",
        "Titulo",
        "Capitulo",
        "Num",
        "Articulo",
        "Inciso",
        "Delito",
        "Activo",
        "PenaMinima",
        "PenaMaxima",
        "Definicion",
        "Jurisprudencia",
        "Notas",
        "CreatorUser",
        "CreatorFullName",
        "CreationDate",
        "CreationIP",
        "UpdaterUser",
        "UpdaterFullName",
        "UpdaterDate",
        "UpdaterIP",
        "Materia",
        "ClaseDelito"
    ];

    protected $guarded  = [];
}