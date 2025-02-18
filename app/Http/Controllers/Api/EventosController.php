<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Eventos;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Validator;

class EventosController extends Controller
{
    //

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


    public function store(Request $request){

        $request->merge([
            'cupo_actual' => is_array($request->cupo_actual) ? $request->cupo_actual : [],
        ]);

        $validacion = Validator::make($request->all(), [
            'nombre' => 'required|max:255',
            'descripcion' => 'required|max:255',
            'tipo_evento' => 'required|in:Taller,Conferencia',
            'ponente_id' => 'required|exists:ponentes,id',
            'dia' => 'required|string|in:Jueves,Viernes',
            'hora_inicio' => 'required|date_format:H:i',
            'cupo_maximo' => 'required|integer|min:1',
            'cupo_actual' => 'array',
            'cupo_actual.*' => 'integer|min:0',
        ]);

        if ($validacion->fails()) {
            return response()->json([
                'mensaje' => 'Error en la validación',
                'errores' => $validacion->errors(),
                'status' => 400,
            ], 400);
        }

        // Calcular la hora de finalización (inicio + 55 minutos)
        $hora_inicio = Carbon::createFromFormat('H:i', $request->hora_inicio);
        $hora_fin = $hora_inicio->copy()->addMinutes(55)->format('H:i');

        // Validar que no haya solapamiento de eventos con el mismo ponente y tipo
        $eventoSolapado = Eventos::validarSolapamiento(
            $request->ponente_id,
            $request->dia,
            $request->hora_inicio,
            $hora_fin,
            $request->tipo_evento
        );

        if ($eventoSolapado) {
            return response()->json([
                'mensaje' => 'El evento se solapa con otro ya existente del mismo tipo en ese horario',
                'status' => 200,
            ], 200);
        }

        // Crear el evento si no hay solapamiento
        $evento = Eventos::create([
            'nombre' => $request->nombre,
            'descripcion' => $request->descripcion,
            'ponente_id' => $request->ponente_id,
            'tipo_evento' => $request->tipo_evento,
            'dia' => $request->dia,
            'hora_inicio' => $request->hora_inicio,
            'hora_fin' => $hora_fin,
            'cupo_maximo' => $request->cupo_maximo,
            'cupo_actual' => $request->cupo_actual,
        ]);

        return response()->json([
            'mensaje' => 'Evento creado correctamente',
            'evento' => $evento,
            'status' => 200,
        ], 200);
    }

    public function destroy($id){

        $evento = Eventos::find($id);

        if(!$evento){
            $data = [
                'mensaje' => 'No se ha podido obtener ponente',
                'status' => 404
            ];
            return response()->json($data, 404);
        }
        $evento->delete();

        $data = [
            'mensaje' => 'Ponente eliminado correctamente',
            'ponente' => $evento,
            'status' => 200
        ];
        return response()->json($data, 200);
    }

}
