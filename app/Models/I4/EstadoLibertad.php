<?php

namespace App\Models\I4;

use Illuminate\Database\Eloquent\Model;

class EstadoLibertad extends Model
{
    protected $connection = 'mysql';
    protected $table      = 'EstadoLibertad';

    protected $fillable = [
        "id",
        "version",
        "EstadoLibertad"
    ];

    protected $guarded  = [];
}