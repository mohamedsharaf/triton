<?php

namespace App\Models\I4;

use Illuminate\Database\Eloquent\Model;

class Dep extends Model
{
    protected $connection = 'mysql';
    protected $table      = 'Dep';

    protected $fillable = [
        "version",
        "Pais",
        "NumDep",
        "Dep",
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