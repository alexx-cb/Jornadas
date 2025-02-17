<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Estudiantes;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class EstudiantesController extends Controller
{

    public function index(){
        $estudiantes = Estudiantes::all();

        if($estudiantes->isEmpty()){
            $data = [
                'mensaje' => 'No hay estudiantes',
                'status' => 200
            ];
            return response()->json($data, 200);
        }

        $data = [
            'estudiantes' => $estudiantes,
            'status' => 200
        ];
        return response()->json($data, 200);
    }

    public function store(Request $request){
        $validator = Validator::make($request->all(), [
            'email' => 'required|string|email|max:255|unique:estudiantes',
        ]);

        if ($validator->fails()) {
            $data = [
                'mensaje' => 'Error en la validacion',
                'status' => 200
            ];
            return response()->json($data, 200);
        }

        $estudiante = Estudiantes::create([
            'email' => $request->input('email'),
        ]);

        if(!$estudiante){
            $data = [
                'mensaje' => 'No se ha podido crear el estudiante',
                'status' => 500
            ];
            return response()->json($data, 500);
        }
        $data = [
            'mensaje' => 'Se ha creado un nuevo estudiante',
            'estudiante' => $estudiante,
            'status' => 200
        ];
        return response()->json($data, 200);

    }

    public function show($id){
        $estudiante = Estudiantes::find($id);

        if(!$estudiante){
            $data = [
                'mensaje' => 'No se ha podido obtener el estudiante',
                'status' => 404
            ];
            return response()->json($data, 404);
        }
        $data = [
            'estudiante' => $estudiante,
            'status' => 200
        ];
        return response()->json($data, 200);
    }

    public function update(Request $request, $id){
        $estudiante = Estudiantes::find($id);

        if(!$estudiante){
            $data = [
                'mensaje' => 'No se ha podido obtener el estudiante',
                'status' => 404
            ];
            return response()->json($data, 404);
        }

        $validator = Validator::make($request->all(), [
           'email' => 'required|string|email|max:255|unique:estudiantes',
        ]);

        if ($validator->fails()) {
            $data = [
                'mensaje' => 'Error en la validacion',
                'status' => 200
            ];
            return response()->json($data, 200);
        }

        $estudiante->email = $request->input('email');
        $estudiante->save();

        $data = [
            'mensaje' => 'Se ha actualizado el estudiante',
            'estudiante' => $estudiante,
            'status' => 200
        ];
        return response()->json($data, 200);
    }

    public function destroy($id){
        $estudiante = Estudiantes::find($id);

        if (!$estudiante) {
            $data = [
                'mensaje' => 'No se ha podido obtener el estudiante',
                'status' => 404
            ];
            return response()->json($data, 404);
        }

        $estudiante->delete();

        $data = [
            'mensaje' => 'Se ha eliminado el estudiante',
            'status' => 200
        ];
        return response()->json($data, 200);
    }
}
