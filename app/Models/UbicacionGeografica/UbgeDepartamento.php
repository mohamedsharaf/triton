<?php

namespace App\Models\UbicacionGeografica;

use Illuminate\Database\Eloquent\Model;

class UbgeDepartamento extends Model
{
    protected $table    = 'ubge_departamentos';

    protected $fillable = [
        'estado',
        'codigo',
        'codigo_2',
        'nombre'
    ];

    protected $guarded  = [];
}
