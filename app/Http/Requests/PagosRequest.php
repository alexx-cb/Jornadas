<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PagosRequest extends FormRequest
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
            default => [],
        };
    }

    protected function storeRules()
    {
        return [
            'user_id' => 'required|integer|exists:users,id|unique:pagos,user_id',
            'tipo_pago' => 'required|string|in:Presencial,Virtual,Gratuito',
            'cantidad' => 'required|numeric',
            'fecha_pago' => 'required|date',
            'estado' =>  'required|string|in:Pagado,Pendiente',
        ];
    }

    public function messages()
    {
        return [
            'user_id.required' => 'El ID del usuario es obligatorio.',
            'user_id.integer' => 'El ID del usuario debe ser un número entero.',
            'user_id.exists' => 'El usuario especificado no existe.',
            'user_id.unique' => 'Ya existe un pago registrado para este usuario.',
            'tipo_pago.required' => 'El tipo de pago es obligatorio.',
            'tipo_pago.in' => 'El tipo de pago debe ser Presencial, Virtual o Gratuito.',
            'cantidad.required' => 'La cantidad es obligatoria.',
            'cantidad.numeric' => 'La cantidad debe ser un valor numérico.',
            'fecha_pago.required' => 'La fecha de pago es obligatoria.',
            'fecha_pago.date' => 'La fecha de pago debe ser una fecha válida.',
            'estado.required' => 'El estado del pago es obligatorio.',
            'estado.in' => 'El estado del pago debe ser Pagado o Pendiente.',
            'id.required' => 'El ID del pago es obligatorio.',
            'id.exists' => 'El pago especificado no existe.',
        ];
    }
}
