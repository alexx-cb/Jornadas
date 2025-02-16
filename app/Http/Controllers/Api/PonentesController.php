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

    public function store(Request $request)
    {
        // Validar los datos
        $validator = Validator::make($request->all(), [
            'nombre' => 'required|string|min:3|max:100',
            'fotografia' => 'required|image|mimes:jpg,jpeg,png,gif,webp|max:2048', // Validar imagen
            'areas_experiencia' => 'required|string|min:3|max:255',
            'redes_sociales' => 'required|string|min:5|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'mensaje' => 'Error en los datos enviados',
                'errores' => $validator->errors()
            ], 400);
        }

        // Subir la imagen a la carpeta public/img
        $fotografia = $request->file('fotografia');

        // Generar un nombre único para la imagen y moverla al directorio public/img
        $fotografiaPath = 'img/' . uniqid('', true) . '.' . $fotografia->getClientOriginalExtension();
        $fotografia->move(public_path('img'), $fotografiaPath);

        // Crear el ponente
        $ponente = Ponentes::create([
            'nombre' => $request->nombre,
            'fotografia' => $fotografiaPath, // Guardamos la ruta de la imagen
            'areas_experiencia' => $request->areas_experiencia,
            'redes_sociales' => $request->redes_sociales,
        ]);

        return response()->json([
            'mensaje' => 'Ponente creado correctamente',
            'ponente' => $ponente
        ], 200);
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
