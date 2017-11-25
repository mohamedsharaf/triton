<?php

namespace App\Models\Seguridad;

use Illuminate\Database\Eloquent\Model;

class SegPermisoRol extends Model
{
  protected $table = 'seg_permisos_roles';

  protected $fillable = [
    'permiso_id',
    'rol_id'
  ];

  protected $guarded  = [];
}
