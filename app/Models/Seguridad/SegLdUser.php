<?php

namespace App\Models\Seguridad;

use Illuminate\Database\Eloquent\Model;

class SegLdUser extends Model
{
    protected $table = 'seg_ld_users';

    protected $fillable = [
        'lugar_dependencia_id',
        'user_id'
    ];

    protected $guarded  = [];
}