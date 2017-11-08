<?php

namespace App\Models\UbicacionGeografica;

use Illuminate\Database\Eloquent\Model;

class UbgeMunicipio extends Model
{
    protected $table    = 'ubge_municipios';

    protected $fillable = [
        'provincia_id',
        'estado',
        'codigo',
        'nombre'
    ];

    protected $guarded  = [];
}
