<?php

namespace App\Models\Seguridad;

use Illuminate\Database\Eloquent\Model;

class SegLdRol extends Model
{
    protected $table = 'seg_ld_roles';

    protected $fillable = [
        'lugar_dependencia_id',
        'rol_id'
    ];

    protected $guarded  = [];
}