<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\EstudiantesRequest;
use App\Models\Estudiantes;

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

    public function store(EstudiantesRequest $request){

        $estudiante = Estudiantes::create([
            'email' => $request->input('email'),
        ]);

        if(!$estudiante){
            $data = [
                'mensaje' => 'Error al crear estudiante',
                'status' => 400
            ];
            return response()->json($data, 400);
        }

        $data = [
            'mensaje' => 'Estudiante creado correctamente',
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

    public function update(EstudiantesRequest $request, $id){

        $estudiante = Estudiantes::find($id);

        if(!$estudiante){
            $data = [
                'mensaje' => 'No se ha podido obtener el estudiante',
                'status' => 404
            ];
            return response()->json($data, 404);
        }

        $estudiante->email = $request->input('email');
        $estudiante->save();

        $data = [
            'mensaje' => 'Estudiante actualizado correctamente',
            'estudiante' => $estudiante,
            'status' => 200
        ];
        return response()->json($data, 200);
    }

    public function destroy($id){

        $estudiante = Estudiantes::find($id);

        if(!$estudiante){
            $data = [
                'mensaje' => 'No se ha podido obtener el estudiante',
                'status' => 404
            ];
            return response()->json($data, 404);
        }

        $estudiante->delete();

        $data = [
            'mensaje' => 'Estudiante eliminado correctamente',
            'status' => 200
        ];
        return response()->json($data, 200);
    }
}
