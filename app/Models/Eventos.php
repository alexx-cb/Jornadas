<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Eventos extends Model
{

    protected $casts = [
        'cupo_actual' => 'array',
    ];

    protected $fillable = [
        'nombre',
        'descripcion',
        'ponente_id',
        'tipo_evento',
        'dia',
        'hora_inicio',
        'hora_fin',
        'cupo_maximo',
        'cupo_actual',
    ];


    // Validar si el evento ya existe (para un ponente en un día y hora específicos)
    public static function validarEventoUnico($ponente_id, $dia, $hora_inicio): bool
    {
        $eventoExistente = self::where('ponente_id', $ponente_id)
            ->where('dia', $dia)
            ->where('hora_inicio', $hora_inicio)
            ->exists();

        return !$eventoExistente;
    }

    // Validar solapamiento de eventos para un ponente en un tipo específico
    public static function validarSolapamiento($ponente_id, $dia, $hora_inicio, $hora_fin, $tipo_evento)
    {
        $eventoSolapado = self::where('ponente_id', $ponente_id)
            ->where('dia', $dia)
            ->where('tipo_evento', $tipo_evento)
            ->where(function ($query) use ($hora_inicio, $hora_fin) {
                // Comprobar si hay solapamiento de horas
                $query->whereBetween('hora_inicio', [$hora_inicio, $hora_fin])
                    ->orWhereBetween('hora_fin', [$hora_inicio, $hora_fin])
                    ->orWhere(function ($query) use ($hora_inicio, $hora_fin) {
                        $query->where('hora_inicio', '<', $hora_inicio)
                            ->where('hora_fin', '>', $hora_fin);
                    });
            })
            ->exists();

        return $eventoSolapado;
    }

    // Verificar que no se solapen los eventos en el mismo día para el mismo tipo de evento
    public static function validarDistribucionEventos($dia, $hora_inicio, $hora_fin, $tipo_evento): bool
    {
        // Buscar si ya hay un evento del mismo tipo en el mismo día y hora
        $eventoSolapado = self::where('dia', $dia)
            ->where('tipo_evento', $tipo_evento)
            ->where(function ($query) use ($hora_inicio, $hora_fin) {
                $query->whereBetween('hora_inicio', [$hora_inicio, $hora_fin])
                    ->orWhereBetween('hora_fin', [$hora_inicio, $hora_fin])
                    ->orWhere(function ($query) use ($hora_inicio, $hora_fin) {
                        $query->where('hora_inicio', '<', $hora_inicio)
                            ->where('hora_fin', '>', $hora_fin);
                    });
            })
            ->exists();

        return !$eventoSolapado;
    }
}
