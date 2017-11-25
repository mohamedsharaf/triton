<?php

namespace App\Models\Institucion;

use Illuminate\Database\Eloquent\Model;

class InstUnidadDesconcentrada extends Model
{
    protected $table = 'inst_unidades_desconcentradas';

    protected $fillable = [
        'lugar_dependencia_id',
        'municipio_id',
        'estado',
        'nombre',
        'direccion'
    ];

    protected $guarded = [];
}
