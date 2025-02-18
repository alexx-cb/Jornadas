<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pagos extends Model
{
    //

    protected $fillable = [
        'user_id',
        'tipo_pago',
        'cantidad',
        'fecha_pago',
        'estado'
    ];
}
