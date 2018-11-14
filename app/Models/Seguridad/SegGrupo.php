<?php

namespace App\Models\Seguridad;

use Illuminate\Database\Eloquent\Model;

class SegGrupo extends Model
{
    protected $table = 'seg_grupos';
    
    protected $fillable = [
        'estado',
        'nombre'
    ];
  
  protected $guarded  = [];
}