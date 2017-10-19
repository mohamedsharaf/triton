<?php

namespace App\Models\Seguridad;

use Illuminate\Database\Eloquent\Model;

class SegPermiso extends Model
{
  protected $table    = 'seg_permisos';
  
  protected $fillable = [
    'modulo_id',
    'estado',
    'codigo',
    'nombre'
  ];
  
  protected $guarded  = [];
}