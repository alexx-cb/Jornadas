<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\PonentesRequest;
use App\Models\Ponentes;
use Illuminate\Support\Facades\Storage;

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

    public function store(PonentesRequest $request){
        $fotografia = $request->file('fotografia');

        if($fotografia->isValid()){
            $fotografiaExtension = $fotografia->getClientOriginalExtension();
            $fotografiaNombre = uniqid('', true) . '_' . time() . '.' . $fotografiaExtension;
            $fotografiaPath = $request->file('fotografia')->storeAs('ponentes', $fotografiaNombre, 'public');
        }

        $ponente = Ponentes::create([
            'nombre' => $request->nombre,
            'fotografia' => $fotografiaPath,
            'areas_experiencia' => $request->areas_experiencia,
            'redes_sociales' => $request->redes_sociales,
        ]);

        $data = [
            'mensaje' => 'Se ha registrado un nuevo ponente',
            'ponente' => $ponente,
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

    public function update(PonentesRequest $request, $id){
        $ponente = Ponentes::find($id);


        if (!$ponente) {
            $data = [
                'mensaje' => 'No se ha podido obtener ponente',
                'status' => 404
            ];
            return response()->json($data, 404);
        }

        $ponente->nombre = $request->nombre;
        $ponente->areas_experiencia = $request->areas_experiencia;
        $ponente->redes_sociales = $request->redes_sociales;

        if ($request->hasFile('fotografia')) {
            if ($ponente->fotografia) {
                Storage::disk('public')->delete($ponente->fotografia);
            }

            $fotografia = $request->file('fotografia');
            $fotografiaExtension = $fotografia->getClientOriginalExtension();
            $fotografiaNombre = uniqid('', true) . '_' . time() . '.' . $fotografiaExtension;
            $fotografiaPath = $fotografia->storeAs('ponentes', $fotografiaNombre, 'public');

            $ponente->fotografia = $fotografiaPath;
        }

        $ponente->save();

        $data = [
            'mensaje' => 'Se ha actualizado el ponente',
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
