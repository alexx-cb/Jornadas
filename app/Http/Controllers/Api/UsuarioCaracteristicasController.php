<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Eventos;
use App\Models\UsuarioCaracteristicas;
use App\Http\Requests\UsuarioCaracteristicasRequest;

class UsuarioCaracteristicasController extends Controller
{
    public function index()
    {
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

    public function update(UsuarioCaracteristicasRequest $request, $id)
    {
        $usuario = UsuarioCaracteristicas::findOrFail($id);

        $usuario->estudiante = $request->estudiante;
        $usuario->tipo_inscripcion = $request->tipo_inscripcion;
        $usuario->save();

        $data = [
            'mensaje' => 'Usuario actualizado correctamente',
            'usuario' => $usuario,
            'status' => 200,
        ];
        return response()->json($data, 200);
    }

    public function inscribirEnEvento(UsuarioCaracteristicasRequest $request, $id)
    {
        $usuario = UsuarioCaracteristicas::findOrFail($id);
        $evento = Eventos::findOrFail($request->evento_id);

        // Verificar límites de talleres y conferencias
        if ($request->tipo === 'taller' && $usuario->talleres >= 4) {
            return response()->json([
                'mensaje' => 'Has alcanzado el límite de talleres',
                'status' => 400
            ], 400);
        } elseif ($request->tipo === 'conferencia' && $usuario->conferencias >= 5) {
            return response()->json([
                'mensaje' => 'Has alcanzado el límite de conferencias',
                'status' => 400
            ], 400);
        }

        // Verificar si el usuario ya está inscrito en este evento
        if (in_array($usuario->user_id, $evento->cupo_actual)) {
            return response()->json([
                'mensaje' => 'Ya estás inscrito en este evento',
                'status' => 400
            ], 400);
        }

        // Actualizar cupo del evento
        $evento->cupo_actual = array_merge($evento->cupo_actual, [$usuario->user_id]);
        $evento->save();

        // Actualizar contador de usuario
        if ($request->tipo === 'taller') {
            $usuario->talleres += 1;
        } else {
            $usuario->conferencias += 1;
        }
        $usuario->save();

        return response()->json([
            'mensaje' => 'Inscripción exitosa',
            'usuario' => $usuario,
            'status' => 200
        ], 200);
    }

    public function show($id)
    {
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
