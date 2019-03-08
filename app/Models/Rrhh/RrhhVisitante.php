<?php

namespace App\Models\Rrhh;

use Illuminate\Database\Eloquent\Model;

class RrhhVisitante extends Model
{
    protected $table = 'rrhh_visitantes';

    protected $fillable = [
        'persona_id',
        'institucion_id',
        'estado',
    ];

    protected $guarded = [];
}
