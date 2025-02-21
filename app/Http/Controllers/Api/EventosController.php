<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\EventosRequest;
use App\Models\Eventos;
use Illuminate\Support\Carbon;

class EventosController extends Controller
{
    public function index(){
        $eventos = Eventos::all();

        if($eventos->isEmpty()){
            $data = [
                'mensaje' => 'No hay eventos registrados',
                'status' => 200,
            ];
            return response()->json($data, 200);
        }

        $data = [
            'eventos' => $eventos,
            'status' => 200,
        ];
        return response()->json($data, 200);
    }

    public function show($id){
        $evento = Eventos::find($id);

        if(!$evento){
            $data = [
                'mensaje' => 'Evento no encontrado',
                'status' => 404,
            ];
            return response()->json($data, 404);
        }
        $data = [
            'evento' => $evento,
            'status' => 200,
        ];
        return response()->json($data, 200);
    }


    public function store(EventosRequest $request)
    {
        $hora_inicio = Carbon::createFromFormat('H:i', $request->hora_inicio);
        $hora_fin = $hora_inicio->copy()->addMinutes(55)->format('H:i');

        $evento = Eventos::create([
            'nombre' => $request->nombre,
            'descripcion' => $request->descripcion,
            'ponente_id' => $request->ponente_id,
            'tipo_evento' => $request->tipo_evento,
            'dia' => $request->dia,
            'hora_inicio' => $request->hora_inicio,
            'hora_fin' => $hora_fin,
            'cupo_maximo' => $request->cupo_maximo,
            'cupo_actual' => $request->cupo_actual ?? [],
        ]);

        $data = [
            'mensaje' => 'Evento creado correctamente',
            'evento' => $evento,
            'status' => 200,
        ];
        return response()->json($data, 200);
    }

    public function updateCupoActual(EventosRequest $request, $id)
    {
        $evento = Eventos::findOrFail($id);


        if (in_array($request->user_id, $evento->cupo_actual)) {
            return response()->json([
                'mensaje' => 'Ya estás registrado en este evento',
                'status' => 400,
            ], 400);
        }


        if (count($evento->cupo_actual) >= $evento->cupo_maximo) {
            return response()->json([
                'mensaje' => 'El evento está completo',
                'status' => 400,
            ], 400);
        }


        return response()->json([
            'mensaje' => 'Inscripción posible',
            'status' => 200,
        ], 200);
    }

    public function destroy($id)
    {
        $evento = Eventos::find($id);

        if(!$evento){
            $data = [
                'mensaje' => 'No se ha podido obtener el evento',
                'status' => 404
            ];
            return response()->json($data, 404);
        }

        if ($evento->cupo_actual->count() > 0) {
            $data = [
                'mensaje' => 'No se puede eliminar el evento si hay usuarios registrados',
                'status' => 400,
            ];
            return response()->json($data, 400);
        }

        $evento->delete();

        $data = [
            'mensaje' => 'Evento eliminado correctamente',
            'evento' => $evento,
            'status' => 200
        ];
        return response()->json($data, 200);
    }

}
