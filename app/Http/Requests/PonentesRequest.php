<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PonentesRequest extends FormRequest
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
            default => [],
        };
    }

    protected function storeRules()
    {
        return [
            'nombre' => 'required|string|min:3|max:100',
            'fotografia' => 'required|image|mimes:jpg,jpeg,png,gif,webp|max:2048',
            'areas_experiencia' => 'required|string|min:3|max:255',
            'redes_sociales' => 'required|string|min:5|max:255',
        ];
    }

    protected function updateRules()
    {
        return [
            'nombre' => 'required|string|min:3|max:100',
            'fotografia' => 'nullable|image|mimes:jpg,jpeg,png,gif,webp|max:2048',
            'areas_experiencia' => 'required|string|min:3|max:255',
            'redes_sociales' => 'required|string|min:5|max:255',
        ];
    }

    public function messages()
    {
        return [
            'nombre.required' => 'El nombre del ponente es obligatorio.',
            'nombre.string' => 'El nombre debe ser una cadena de texto.',
            'nombre.min' => 'El nombre debe tener al menos 3 caracteres.',
            'nombre.max' => 'El nombre no puede tener más de 100 caracteres.',
            'fotografia.required' => 'La fotografía es obligatoria.',
            'fotografia.image' => 'El archivo debe ser una imagen.',
            'fotografia.mimes' => 'La imagen debe ser de tipo: jpg, jpeg, png, gif o webp.',
            'fotografia.max' => 'La imagen no puede ser mayor a 2MB.',
            'areas_experiencia.required' => 'Las áreas de experiencia son obligatorias.',
            'areas_experiencia.string' => 'Las áreas de experiencia deben ser una cadena de texto.',
            'areas_experiencia.min' => 'Las áreas de experiencia deben tener al menos 3 caracteres.',
            'areas_experiencia.max' => 'Las áreas de experiencia no pueden tener más de 255 caracteres.',
            'redes_sociales.required' => 'Las redes sociales son obligatorias.',
            'redes_sociales.string' => 'Las redes sociales deben ser una cadena de texto.',
            'redes_sociales.min' => 'Las redes sociales deben tener al menos 5 caracteres.',
            'redes_sociales.max' => 'Las redes sociales no pueden tener más de 255 caracteres.',
        ];
    }
}
