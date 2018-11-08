<?php

namespace App\Models\I4;

use Illuminate\Database\Eloquent\Model;

class CasoDelito extends Model
{
    protected $connection = 'mysql';
    protected $table      = 'CasoDelito';

    protected $fillable = [
        "version",
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
        "Delito",
        "Tentativa"
    ];

    protected $guarded  = [];
}