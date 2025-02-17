<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Ponentes;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PonentesController extends Controller
{

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
            'fotografia' => 'required|image|mimes:jpg,jpeg,png,gif,webp|max:2048',
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
            'fotografia' => $fotografiaPath,
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
        // Buscar el ponente
        $ponente = Ponentes::find($id);
        if (!$ponente) {
            return response()->json([
                'mensaje' => 'Ponente no encontrado',
                'status' => 404
            ], 404);
        }



        // Validación de los datos del formulario
        $validator = Validator::make($request->all(), [
            'nombre' => 'required|string|min:3|max:100',
            'fotografia' => 'required|image|mimes:jpg,jpeg,png,gif,webp|max:2048',
            'areas_experiencia' => 'required|string|min:3|max:255',
            'redes_sociales' => 'required|string|min:5|max:255',
        ]);

        // Validación fallida
        if ($validator->fails()) {
            return response()->json([
                'mensaje' => 'Error en la validación',
                'errores' => $validator->errors()
            ], 400);
        }

        // Actualizar los datos del ponente
        $ponente->nombre = $request->nombre;
        $ponente->areas_experiencia = $request->areas_experiencia;
        $ponente->redes_sociales = $request->redes_sociales;

        // Manejo de la fotografía (si se envía un nuevo archivo)
        if ($request->hasFile('fotografia')) {
            // Eliminar la fotografía anterior si existe
            if ($ponente->fotografia && file_exists(public_path('img/' . $ponente->fotografia))) {
                unlink(public_path('img/' . $ponente->fotografia));
            }

            // Subir la nueva fotografía
            $fotografia = $request->file('fotografia');
            $fotografiaPath = 'img/' . uniqid('', true) . '.' . $fotografia->getClientOriginalExtension();
            $fotografia->move(public_path('img'), $fotografiaPath);

            // Asignar la nueva fotografía al ponente
            $ponente->fotografia = $fotografiaPath;
        }

        // Guardar los cambios
        $ponente->save();

        // Responder con el estado y los datos actualizados
        return response()->json([
            'mensaje' => 'Ponente actualizado correctamente',
            'ponente' => $ponente,
            'status' => 200
        ], 200);
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
