<?php

namespace App\Models\Institucion;

use Illuminate\Database\Eloquent\Model;

class InstInstitucion extends Model
{
    protected $table = 'inst_instituciones';

    protected $fillable = [
        'institucion_id',
        'ubge_municipios_id',
        'estado',
        'nombre',
        'zona',
        'direccion',
        'telefono',
        'celular',
        'email'
    ];

    protected $guarded = [];
}
