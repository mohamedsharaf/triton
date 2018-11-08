<?php

namespace App\Models\I4;

use Illuminate\Database\Eloquent\Model;

class EtapaCaso extends Model
{
    protected $connection = 'mysql';
    protected $table      = 'EtapaCaso';

    protected $fillable = [
        "version",
        "EtapaCaso"
    ];

    protected $guarded  = [];
}