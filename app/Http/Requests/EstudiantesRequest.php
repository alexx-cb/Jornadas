<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class EstudiantesRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules(): array{
        $method = $this->method();
        $estudiante_id = $this->route('id');

        return match($method) {
            'POST' => $this->storeRules(),
            'PUT', 'PATCH' => $this->updateRules($estudiante_id),
            'DELETE' => $this->deleteRules(),
            default => [],
        };
    }

    protected function storeRules():array {
        return [
            'email' => 'required|string|email|max:255|unique:estudiantes',
        ];
    }

    protected function updateRules($id):array{
        return [
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique('estudiantes')->ignore($id),
            ],
        ];
    }


    public function messages(): array{
        return [
            'email.required' => 'El campo email es obligatorio.',
            'email.string' => 'El email debe ser una cadena de texto.',
            'email.email' => 'El email debe ser una dirección de correo válida.',
            'email.max' => 'El email no puede tener más de 255 caracteres.',
            'email.unique' => 'Este email ya está registrado.'
        ];
    }
}
