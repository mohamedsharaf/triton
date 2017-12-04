<?php

namespace App\Models\Institucion;

use Illuminate\Database\Eloquent\Model;

class InstCargo extends Model
{
    protected $table = 'inst_cargos';

    protected $fillable = [
        'auo_id',
        'cargo_id',
        'tipo_cargo_id',
        'estado',
        'item_contrato',
        'acefalia',
        'nombre'
    ];

    protected $guarded = [];
}