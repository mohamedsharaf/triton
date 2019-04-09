<?php

namespace App\Models\I4;

use Illuminate\Database\Eloquent\Model;

class EstadoNotificacion extends Model
{
    protected $connection = 'mysql';
    protected $table      = 'EstadoNotificacion';

    protected $fillable = [
        "version",
        "EstadoNotificacion",
        "UsoEntrega"
    ];

    protected $guarded  = [];
}