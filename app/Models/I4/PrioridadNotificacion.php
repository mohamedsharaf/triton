<?php

namespace App\Models\I4;

use Illuminate\Database\Eloquent\Model;

class PrioridadNotificacion extends Model
{
    protected $connection = 'mysql';
    protected $table      = 'PrioridadNotificacion';

    protected $fillable = [
        "version",
        "PrioridadNotificacion"
    ];

    protected $guarded  = [];
}