<?php

namespace App\Models\I4;

use Illuminate\Database\Eloquent\Model;

class NotificacionEntrega extends Model
{
    protected $connection = 'mysql';
    protected $table      = 'NotificacionEntrega';

    protected $fillable = [
        "version",
        "FechaEntregaFisica",
        "CreatorUser",
        "CreatorFullName",
        "CreationDate",
        "CreationIP",
        "UpdaterUser",
        "UpdaterFullName",
        "UpdaterDate",
        "UpdaterIP",
        "Notificacion",
        "FuncionarioEntrega",
        "EstadoNotificacion"
    ];

    protected $guarded  = [];
}