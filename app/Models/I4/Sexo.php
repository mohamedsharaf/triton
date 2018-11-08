<?php

namespace App\Models\I4;

use Illuminate\Database\Eloquent\Model;

class Sexo extends Model
{
    protected $connection = 'mysql';
    protected $table      = 'Sexo';

    protected $fillable = [
        "version",
        "Sexo"
    ];

    protected $guarded  = [];
}