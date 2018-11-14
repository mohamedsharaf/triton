<?php

namespace App\Models\I4;

use Illuminate\Database\Eloquent\Model;

class RecintoCarcelario extends Model
{
    protected $connection = 'mysql';
    protected $table      = 'RecintosCarcelarios';

    protected $fillable = [
        "Muni_id",
        "estado",
        "tipo_recinto",
        "nombre"
    ];

    protected $guarded  = [];
}