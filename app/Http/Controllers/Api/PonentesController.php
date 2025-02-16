<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Ponentes;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PonentesController extends Controller
{
    //

    public function index(){
        $ponentes = Ponentes::all();

        if($ponentes->isEmpty()){
            $data = [
                'mensaje' => 'No hay ponentes registrados',
                'status' => 200
            ];
            return response()->json($data, 200);
        }

        $data = [
            'ponentes' => $ponentes,
            'status' => 200
        ];
        return response()->json($data, 200);

    }

    public function store(Request $request){
        $ponente = Ponentes::create([
            'nombre' => $request->nombre,
            'fotografia' => $request->fotografia,
            'areas_experiencia' => $request->areas_experiencia,
            'redes_sociales' => $request->redes_sociales
        ]);

        if(!$ponente){
            $data = [
                'mensaje' => 'No se ha podido crear ponente',
                'status' => 500
            ];
            return response()->json($data, 500);
        }
        $data = [
            'mensaje' => 'Ponente creado correctamente',
            'ponentes' => $ponente,
            'status' => 200
        ];
        return response()->json($data, 200);
    }

    public function show($id){

        $ponente = Ponentes::find($id);

        if(!$ponente){
            $data = [
                'mensaje' => 'No se ha podido obtener ponente',
                'status' => 404
            ];
            return response()->json($data, 404);
        }

        $data = [
            'ponente' => $ponente,
            'status' => 200
        ];
        return response()->json($data, 200);
    }

    public function update(Request $request, $id){
        $ponente = Ponentes::find($id);

        if(!$ponente){
            $data = [
                'mensaje' => 'No se ha podido obtener ponente',
                'status' => 404
            ];
            return response()->json($data, 404);
        }


        $validator = Validator::make($request->all(), [
            'nombre' => 'required|string|min:3|max:100',
            'fotografia' => 'required|string|min:5|max:100',
            'areas_experiencia' => 'required|string|min:3|max:100',
            'redes_sociales' => 'required|string',
        ]);

        if($validator->fails()){
            $data = [
                'mensaje' => 'Error en la validacion de los datos',
                'error' => $validator->errors(),
                'status' => 400
            ];
            return response()->json($data, 400);
        }

        $ponente->nombre = $request->input('nombre');
        $ponente->fotografia = $request->input('fotografia');
        $ponente->areas_experiencia = $request->input('areas_experiencia');
        $ponente->redes_sociales = $request->input('redes_sociales');
        $ponente->save();

        $data = [
            'mensaje' => 'Ponente actualizado correctamente',
            'ponente' => $ponente,
            'status' => 200
        ];
        return response()->json($data, 200);

    }

    public function destroy($id){
        $ponente = Ponentes::find($id);

        if(!$ponente){
            $data = [
                'mensaje' => 'No se ha podido obtener ponente',
                'status' => 404
            ];
            return response()->json($data, 404);
        }
        $ponente->delete();

        $data = [
            'mensaje' => 'Ponente eliminado correctamente',
            'ponente' => $ponente,
            'status' => 200
        ];
        return response()->json($data, 200);
    }

}
