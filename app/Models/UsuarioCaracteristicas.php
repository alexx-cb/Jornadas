<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UsuarioCaracteristicas extends Model
{
    //

    protected $fillable = [
        'user_id',
        'email',
        'tipo_inscripcion',
        'estudiante',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
