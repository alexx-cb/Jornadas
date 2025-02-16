<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Ponentes extends Model
{
    //

    protected $fillable = [
        'nombre',
        'fotografia',
        'areas_experiencia',
        'redes_sociales',
    ];
}
