<?php

namespace App\Models\I4;

use Illuminate\Database\Eloquent\Model;

class Muni extends Model
{
    protected $connection = 'mysql';
    protected $table      = 'Muni';

    protected $fillable = [
        "version",
        "Dep",
        "NumMuni",
        "Muni",
        "Urbano",
        "CreatorUser",
        "CreatorFullName",
        "CreationDate",
        "CreationIP",
        "UpdaterUser",
        "UpdaterFullName",
        "UpdaterDate",
        "UpdaterIP"
    ];

    protected $guarded  = [];
}