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
            $data = [
                'mensaje' => 'Usuario no encontrado',
                'status' => 404,
            ];
            return response()->json($data, 404);
        }

        $validator = Validator::make($request->all(), [
            'estudiante' => 'required|boolean',
            'tipo_inscripcion' => 'required|integer|in:1,2,3',
        ]);

        if ($validator->fails()) {
            $data = [
                'mensaje' => 'Error en la validacion',
                'errors' => $validator->errors(),
                'status' => 400,
            ];
            return response()->json($data, 400);
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
                $data = [
                    'mensaje' => 'Debes completar el pago antes de actualizar tu inscripción',
                    'status' => 400,
                ];
                return response()->json($data, 400);
            }
        }

        $esEstudiante = $request->input('estudiante');
        $esEstudianteVerificado = Estudiantes::where('email', $usuario->user->email)->exists();

        if ($esEstudiante && !$esEstudianteVerificado) {

            $data = [
                'mensaje' => 'No puedes seleccionar "Estudiante" si tu email no está registrado como estudiante.',
                'status' => 400,
            ];
            return response()->json($data, 400);
        }


        $usuario->estudiante = $esEstudiante;
        $usuario->tipo_inscripcion = $tipoInscripcion;
        $usuario->save();

        $data = [
            'mensaje' => 'Usuario actualizado correctamente',
            'usuario' => $usuario,
            'status' => 200,
        ];
        return response()->json($data, 200);
    }

    public function inscribirEnEvento(Request $request, $id){
        $usuario = UsuarioCaracteristicas::find($id);

        if (!$usuario) {
            $data = [
                'mensaje' => 'Usuario no encontrado',
                'status' => 404,
            ];
            return response()->json($data, 404);
        }

        $validator = Validator::make($request->all(), [
            'tipo' => 'required|string|in:taller,conferencia'
        ]);

        if ($validator->fails()) {
            $data = [
                'mensaje' => 'Error en la validacion',
                'errors' => $validator->errors(),
                'status' => 400,
            ];
            return response()->json($data, 400);
        }

        $pagoRealizado = Pagos::where('user_id', $usuario->user_id)
            ->where('estado', 'Pagado') // Asegurar que el estado es "Pagado"
            ->exists();

        if (!$pagoRealizado) {
            $data = [
                "mensaje" => 'Debes completar el pago antes de actualizar tu pago',
                'status' => 400,
            ];
            return response()->json($data, 400);
        }

        if ($request->tipo === 'taller' && $usuario->talleres < 4) {
            $usuario->talleres += 1;
        } elseif ($request->tipo === 'conferencia' && $usuario->conferencias < 5) {
            $usuario->conferencias += 1;
        } else {
            $data = [
                'mensaje' => 'no se puede inscribir a mas eventos del tipo' . $request->tipo,
                'status' => 400,
            ];
            return response()->json($data, 400);
        }

        $usuario->save();

        $data = [
            'mensaje' => 'Inscripcion exitosa',
            'usuario' => $usuario,
            'status' => 200
        ];
        return response()->json($data, 200);
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
