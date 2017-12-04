<?php

namespace App\Models\Institucion;

use Illuminate\Database\Eloquent\Model;

class InstTipoCargo extends Model
{
    protected $table = 'inst_auos';

    protected $fillable = [
        'estado',
        'nombre'
    ];

    protected $guarded = [];
}