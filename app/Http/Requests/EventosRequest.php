<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\Eventos;
use Illuminate\Support\Carbon;

class EventosRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        $method = $this->method();

        return match($method) {
            'POST' => $this->storeRules(),
            'PUT', 'PATCH' => $this->updateRules(),
            'DELETE' => $this->deleteRules(),
            default => [],
        };
    }

    protected function storeRules()
    {
        return [
            'nombre' => 'required|max:255',
            'descripcion' => 'required|max:255',
            'tipo_evento' => 'required|in:Taller,Conferencia',
            'ponente_id' => 'required|exists:ponentes,id',
            'dia' => 'required|string|in:Jueves,Viernes',
            'hora_inicio' => 'required|date_format:H:i',
            'cupo_maximo' => 'required|integer|min:1',
            'cupo_actual' => 'array',
            'cupo_actual.*' => 'integer|min:0',
        ];
    }

    protected function updateRules()
    {
        return [
            'user_id' => 'required|exists:users,id',
        ];
    }

    protected function deleteRules()
    {
        return [
            'id' => 'required|exists:eventos,id',
        ];
    }

    public function messages()
    {
        return [
            'nombre.required' => 'El nombre del evento es obligatorio.',
            'nombre.max' => 'El nombre del evento no puede exceder los 255 caracteres.',
            'descripcion.required' => 'La descripción del evento es obligatoria.',
            'descripcion.max' => 'La descripción del evento no puede exceder los 255 caracteres.',
            'tipo_evento.required' => 'El tipo de evento es obligatorio.',
            'tipo_evento.in' => 'El tipo de evento debe ser Taller o Conferencia.',
            'ponente_id.required' => 'El ID del ponente es obligatorio.',
            'ponente_id.exists' => 'El ponente seleccionado no existe.',
            'dia.required' => 'El día del evento es obligatorio.',
            'dia.in' => 'El día debe ser Jueves o Viernes.',
            'hora_inicio.required' => 'La hora de inicio es obligatoria.',
            'hora_inicio.date_format' => 'La hora de inicio debe tener el formato HH:mm.',
            'cupo_maximo.required' => 'El cupo máximo es obligatorio.',
            'cupo_maximo.integer' => 'El cupo máximo debe ser un número entero.',
            'cupo_maximo.min' => 'El cupo máximo debe ser al menos 1.',
            'cupo_actual.array' => 'El cupo actual debe ser un array.',
            'user_id.required' => 'El ID del usuario es obligatorio.',
            'user_id.exists' => 'El usuario seleccionado no existe.',
            'id.required' => 'El ID del evento es obligatorio.',
            'id.exists' => 'El evento seleccionado no existe.',
            'evento_unico' => 'Ya existe un evento para este ponente en el día y hora especificados.',
            'solapamiento' => 'El evento se solapa con otro ya existente del mismo tipo en ese horario.',
            'distribucion_eventos' => 'Ya existe un evento del mismo tipo en el mismo día y hora.',
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            if ($this->isMethod('POST')) {
                $hora_inicio = Carbon::createFromFormat('H:i', $this->hora_inicio);
                $hora_fin = $hora_inicio->copy()->addMinutes(55)->format('H:i');

                // Validar evento único
                if (!Eventos::validarEventoUnico($this->ponente_id, $this->dia, $this->hora_inicio)) {
                    $validator->errors()->add('evento_unico', 'Ya existe un evento para este ponente en el día y hora especificados.');
                }

                // Validar solapamiento
                if (Eventos::validarSolapamiento($this->ponente_id, $this->dia, $this->hora_inicio, $hora_fin, $this->tipo_evento)) {
                    $validator->errors()->add('solapamiento', 'El evento se solapa con otro ya existente del mismo tipo en ese horario.');
                }

                // Validar distribución de eventos
                if (!Eventos::validarDistribucionEventos($this->dia, $this->hora_inicio, $hora_fin, $this->tipo_evento)) {
                    $validator->errors()->add('distribucion_eventos', 'Ya existe un evento del mismo tipo en el mismo día y hora.');
                }
            }
        });
    }
}
