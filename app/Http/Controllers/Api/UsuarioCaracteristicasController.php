<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Estudiantes;
use App\Models\Pagos;
use App\Models\UsuarioCaracteristicas;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class UsuarioCaracteristicasController extends Controller
{
    //
    public function index(){
        $usuario = UsuarioCaracteristicas::all();

        if($usuario->isEmpty()){
            $data = [
                'mensaje' => 'No hay usuario registrado',
                'status' => '200',
            ];
            return response()->json($data, 200);
        }
        $data = [
            'usuarios' => $usuario,
            'status' => '200',
        ];
        return response()->json($data, 200);
    }


    public function update(Request $request, $id){
        $usuario = UsuarioCaracteristicas::find($id);

        if (!$usuario) {
            return response()->json([
                'mensaje' => 'Usuario no encontrado',
                'status' => 404
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'estudiante' => 'required|boolean',
            'tipo_inscripcion' => 'required|integer|in:1,2,3',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'mensaje' => 'Error en la validación del usuario',
                'errors' => $validator->errors(),
                'status' => 400
            ], 400);
        }

        $tipoInscripcion = $request->input('tipo_inscripcion');
        $userId = $usuario->user_id;

        if ($tipoInscripcion == 1 || $tipoInscripcion == 2) {
            $precioInscripcion = ($tipoInscripcion == 1) ? 15 : 7;
            $pagoRealizado = Pagos::where('user_id', $userId)
                ->where('cantidad', $precioInscripcion)
                ->where('estado', 'Pagado')
                ->exists();

            if (!$pagoRealizado) {
                return response()->json([
                    'mensaje' => 'Debes completar el pago antes de actualizar tu inscripción.',
                    'status' => 403
                ], 403);
            }
        }

        $esEstudiante = $request->input('estudiante');
        $esEstudianteVerificado = Estudiantes::where('email', $usuario->user->email)->exists();

        if ($esEstudiante && !$esEstudianteVerificado) {
            return response()->json([
                'mensaje' => 'No puedes seleccionar "Estudiante" si tu email no está registrado como estudiante.',
                'status' => 403
            ], 403);
        }


        $usuario->estudiante = $esEstudiante;
        $usuario->tipo_inscripcion = $tipoInscripcion;
        $usuario->save();

        return response()->json([
            'mensaje' => 'Usuario actualizado con éxito.',
            'usuario' => $usuario,
            'status' => 200
        ], 200);
    }

    public function inscribirEnEvento(Request $request, $id){
        $usuario = UsuarioCaracteristicas::find($id);

        if (!$usuario) {
            return response()->json([
                'mensaje' => 'Usuario no encontrado',
                'status' => 404,
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'tipo' => 'required|string|in:taller,conferencia'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'mensaje' => 'Error en la validación',
                'errors' => $validator->errors(),
                'status' => 400,
            ], 400);
        }

        $pagoRealizado = Pagos::where('user_id', $usuario->user_id)
            ->where('estado', 'Pagado') // Asegurar que el estado es "Pagado"
            ->exists();

        if (!$pagoRealizado) {
            return response()->json([
                'mensaje' => 'No puedes inscribirte en eventos hasta completar el pago.',
                'status' => 403,
            ], 403);
        }

        if ($request->tipo === 'taller' && $usuario->talleres < 4) {
            $usuario->talleres += 1;
        } elseif ($request->tipo === 'conferencia' && $usuario->conferencias < 5) {
            $usuario->conferencias += 1;
        } else {
            return response()->json([
                'mensaje' => 'Límite alcanzado para ' . $request->tipo,
                'status' => 400,
            ], 400);
        }

        $usuario->save();

        return response()->json([
            'mensaje' => 'Inscripción exitosa en ' . $request->tipo,
            'usuario' => [
                'user_id' => $usuario->user_id,
                'email' => $usuario->email,
                'tipo_inscripcion' => $usuario->tipo_inscripcion,
                'estudiante' => $usuario->estudiante,
                'talleres' => $usuario->talleres,
                'conferencias' => $usuario->conferencias
            ],
            'status' => 200,
        ], 200);
    }

    public function show($id){
        $usuario = UsuarioCaracteristicas::find($id);

        if(!$usuario){
            $data = [
                'mensaje' => 'Usuario no encontrado',
                'status' => 404,
            ];
            return response()->json($data, 404);
        }

        $data = [
            'usuario' => $usuario,
            'status' => 200,
        ];
        return response()->json($data, 200);

    }

}
