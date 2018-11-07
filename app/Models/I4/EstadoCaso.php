<?php

namespace App\Models\I4;

use Illuminate\Database\Eloquent\Model;

class EstadoCaso extends Model
{
    protected $connection = 'mysql';
    protected $table      = 'EstadoCaso';

    protected $fillable = [
        "id",
        "version",
        "EstadoCaso"
    ];

    protected $guarded  = [];
}