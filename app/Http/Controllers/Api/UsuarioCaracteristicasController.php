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


}
