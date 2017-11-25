<?php

namespace App\Models\Seguridad;

use Illuminate\Database\Eloquent\Model;

class SegRol extends Model
{
  protected $table = 'seg_roles';

  protected $fillable = [
    'estado',
    'nombre'
  ];

  protected $guarded  = [];
}
