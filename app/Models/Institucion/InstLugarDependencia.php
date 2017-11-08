<?php

namespace App\Models\Institucion;

use Illuminate\Database\Eloquent\Model;

class InstLugarDependencia extends Model
{
    protected $table = 'inst_lugares_dependencia';

    protected $fillable = [
        'estado',
        'nombre'
    ];

    protected $guarded = [];
}
