<?php

namespace App\Models\Institucion;

use Illuminate\Database\Eloquent\Model;

class InstTipoCargo extends Model
{
    protected $table = 'inst_tipos_cargo';

    protected $fillable = [
        'estado',
        'nombre'
    ];

    protected $guarded = [];
}