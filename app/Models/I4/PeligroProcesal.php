<?php

namespace App\Models\I4;

use Illuminate\Database\Eloquent\Model;

class PeligroProcesal extends Model
{
    protected $connection = 'mysql';
    protected $table      = 'PeligrosProcesales';

    protected $fillable = [
        "estado",
        "nombre"
    ];

    protected $guarded  = [];
}