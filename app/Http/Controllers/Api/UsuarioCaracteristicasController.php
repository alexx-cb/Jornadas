<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
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

        if(!$usuario){
            $data = [
                'mensaje' => 'Usuario no encontrado',
                'status' => '404',
            ];
            return response()->json($data, 400);
        }

        $validator = Validator::make($request->all(), [
            'estudiante' => 'required|boolean',
            'tipo_inscripcion' => 'required|integer',
            'talleres' => 'required|integer',
            'conferencias' => 'required|integer',
        ]);

        if($validator->fails()){
            $data = [
                'mensaje' => 'Error en la validacion',
                'errors' => $validator->errors(),
                'status' => '400',
            ];
            return response()->json($data, 400);
        }

        $usuario->estudiante = $request->input('estudiante');
        $usuario->tipo_inscripcion = $request->input('tipo_inscripcion');

        $usuario->save();

        $data = [
            'mensaje' => 'Usuario actualizado con exito',
            'usuario' => $usuario,
            'status' => '200',
        ];
        return response()->json($data, 200);
    }

    public function inscribirEnEvento(Request $request, $id){
        $usuario = UsuarioCaracteristicas::find($id);

        if(!$usuario){
            return response()->json([
                'mensaje' => 'Usuario no encontrado',
                'status' => '404',
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'tipo' => 'required|string|in:taller,conferencia'
        ]);

        if($validator->fails()){
            return response()->json([
                'mensaje' => 'Error en la validación',
                'errors' => $validator->errors(),
                'status' => '400',
            ], 400);
        }

        if ($request->tipo === 'taller' && $usuario->talleres < 4) {
            $usuario->talleres += 1;
        } elseif ($request->tipo === 'conferencia' && $usuario->conferencias < 5) {
            $usuario->conferencias += 1;
        } else {
            return response()->json([
                'mensaje' => 'Límite alcanzado para ' . $request->tipo,
                'status' => '400',
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
            'status' => '200',
        ], 200);
    }


}
