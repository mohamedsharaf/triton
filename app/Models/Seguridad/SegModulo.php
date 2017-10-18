<?php

namespace App\Models\Seguridad;

use Illuminate\Database\Eloquent\Model;

class SegModulo extends Model
{
  protected $table    = 'seg_modulos';
  
  protected $fillable = [
    'estado',
    'codigo',
    'nombre'
  ];
  
  protected $guarded  = [];
}