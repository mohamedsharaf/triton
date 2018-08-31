<?php

namespace App\Models\Dpvt;

use Illuminate\Database\Eloquent\Model;

class PvtDelito extends Model
{
    protected $table = 'pvt_delitos';

    protected $fillable = [
        'estado',
        'codigo',
        'libro',
        'n_libro',
        'n_titulo',
        'n_capitulo',
        'n_delito',
        'n_articulo',
        'inciso',
        'nombre'
    ];

    protected $guarded  = [];
}