<?php

namespace App\Models\I4;

use Illuminate\Database\Eloquent\Model;

class PersonaPeligroProcesal extends Model
{
    protected $connection = 'mysql';
    protected $table      = 'PersonaPeligrosProcesales';

    protected $fillable = [
        "persona_id",
        "peligro_procesal_id"
    ];

    protected $guarded  = [];
}