<?php

namespace App\Models\Institucion;

use Illuminate\Database\Eloquent\Model;

class InstAuo extends Model
{
    protected $table = 'inst_auos';

    protected $fillable = [
        'lugar_dependencia_id',
        'auo_id',
        'estado',
        'nombre'
    ];

    protected $guarded = [];
}