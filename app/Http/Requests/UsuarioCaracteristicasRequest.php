<?php

namespace App\Http\Requests;

use App\Models\UsuarioCaracteristicas;
use Illuminate\Foundation\Http\FormRequest;
use App\Models\Estudiantes;
use App\Models\Pagos;

class UsuarioCaracteristicasRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        $method = $this->method();

        return match($method) {
            'PUT', 'PATCH' => $this->updateRules(),
            'POST' => $this->inscribirEventoRules(),
            default => [],
        };
    }

    protected function updateRules()
    {
        return [
            'estudiante' => 'required|boolean',
            'tipo_inscripcion' => 'required|integer|in:1,2,3',
        ];
    }

    protected function inscribirEventoRules()
    {
        $usuario = UsuarioCaracteristicas::findOrFail($this->route('id'));

        return [
            'tipo' => [
                'required',
                'string',
                'in:taller,conferencia',
                function ($attribute, $value, $fail) use ($usuario) {
                    if ($value === 'taller' && $usuario->talleres >= 4) {
                        $fail('No se puede inscribir a más talleres');
                    } elseif ($value === 'conferencia' && $usuario->conferencias >= 5) {
                        $fail('No se puede inscribir a más conferencias');
                    }
                },
            ],
        ];
    }

    public function messages()
    {
        return [
            'estudiante.required' => 'El campo estudiante es obligatorio.',
            'estudiante.boolean' => 'El campo estudiante debe ser verdadero o falso.',
            'tipo_inscripcion.required' => 'El tipo de inscripción es obligatorio.',
            'tipo_inscripcion.integer' => 'El tipo de inscripción debe ser un número entero.',
            'tipo_inscripcion.in' => 'El tipo de inscripción debe ser 1, 2 o 3.',
            'tipo.required' => 'El tipo de evento es obligatorio.',
            'tipo.string' => 'El tipo de evento debe ser una cadena de texto.',
            'tipo.in' => 'El tipo de evento debe ser taller o conferencia.',
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            if ($this->isMethod('PUT') || $this->isMethod('PATCH')) {
                $this->validateUpdateRules($validator);
            } elseif ($this->isMethod('POST')) {
                $this->validateInscribirEventoRules($validator);
            }
        });
    }

    protected function validateUpdateRules($validator)
    {
        $esEstudiante = $this->input('estudiante');
        $tipoInscripcion = $this->input('tipo_inscripcion');
        $usuario = $this->route('usuario_caracteristica');

        if (!$esEstudiante && $tipoInscripcion == 3) {
            $validator->errors()->add('tipo_inscripcion', 'No se puede actualizar a tipo de suscripción 3 si no eres estudiante');
        }

        if ($tipoInscripcion == 1 || $tipoInscripcion == 2) {
            $precioInscripcion = ($tipoInscripcion == 1) ? 15 : 7;
            $pagoRealizado = Pagos::where('user_id', $usuario->user_id)
                ->where('cantidad', $precioInscripcion)
                ->where('estado', 'Pagado')
                ->exists();

            if (!$pagoRealizado) {
                $validator->errors()->add('tipo_inscripcion', 'Debes completar el pago antes de actualizar tu inscripción');
            }
        }

        $esEstudianteVerificado = Estudiantes::where('email', $usuario->user->email)->exists();

        if ($esEstudiante && !$esEstudianteVerificado) {
            $validator->errors()->add('estudiante', 'No puedes seleccionar "Estudiante" si tu email no está registrado como estudiante.');
        }
    }

    protected function validateInscribirEventoRules($validator)
    {
        $usuario = $this->route('usuario_caracteristica');

        $pagoRealizado = Pagos::where('user_id', $usuario->user_id)
            ->where('estado', 'Pagado')
            ->exists();

        if (!$pagoRealizado) {
            $validator->errors()->add('pago', 'Debes completar el pago antes de actualizar tu perfil');
        }

        if ($this->tipo === 'taller' && $usuario->talleres >= 4) {
            $validator->errors()->add('tipo', 'No se puede inscribir a más talleres');
        } elseif ($this->tipo === 'conferencia' && $usuario->conferencias >= 5) {
            $validator->errors()->add('tipo', 'No se puede inscribir a más conferencias');
        }
    }
}
