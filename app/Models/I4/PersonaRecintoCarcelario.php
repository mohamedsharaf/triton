<?php

namespace App\Models\I4;

use Illuminate\Database\Eloquent\Model;

class PersonaRecintoCarcelario extends Model
{
    protected $connection = 'mysql';
    protected $table      = 'PersonaRecintosCarcelarios';

    protected $fillable = [
        "persona_id",
        "recinto_carcelario_id"
    ];

    protected $guarded  = [];
}