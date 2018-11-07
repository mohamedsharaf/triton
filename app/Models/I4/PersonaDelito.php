<?php

namespace App\Models\I4;

use Illuminate\Database\Eloquent\Model;

class PersonaDelito extends Model
{
    protected $connection = 'mysql';
    protected $table      = 'PersonaDelito';

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
        "Persona",
        "Delito",
        "Tentativa"
    ];

    protected $guarded  = [];
}