<?php

namespace App\Models\Dpvt;

use Illuminate\Database\Eloquent\Model;

class PvtDerivacion extends Model
{
    protected $table = 'pvt_derivaciones';

    protected $fillable = [
        'institucion_id',
        'visitante_id',
        'user_id',
        'estado',
        'motivo',
        'relato',
        'fecha',
        'codigo'
    ];

    protected $guarded = [];
}
