<?php

namespace App\Models\UbicacionGeografica;

use Illuminate\Database\Eloquent\Model;

class UbgeProvincia extends Model
{
    protected $table    = 'ubge_provincias';

    protected $fillable = [
        'departamento_id',
        'estado',
        'codigo',
        'nombre'
    ];

    protected $guarded  = [];
}
