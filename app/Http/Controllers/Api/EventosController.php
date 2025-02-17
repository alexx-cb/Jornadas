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

        $validacion = Validator::make($request->all(), [
            'nombre' => 'required|max:255',
            'descripcion' => 'required|max:255',
            'tipo_evento' => 'required|in:Taller,Conferencia',
            'ponente_id' => 'required|exists:ponentes,id',
            'dia' => 'required|string|in:Jueves,Viernes',
            'hora_inicio' => 'required|date_format:H:i',
            'cupo_maximo' => 'required|integer|min:1',
            'cupo_actual' => 'required|integer|min:0',
        ]);


        if ($validacion->fails()) {
            return response()->json([
                'mensaje' => 'Error en la validación',
                'errores' => $validacion->errors(),
                'status' => 400,
            ], 400);
        }

        // Validar que no haya evento ya asignado para ese ponente en mismo día y hora
        $validacionUnica = Eventos::validarEventoUnico(
            $request->ponente_id,
            $request->dia,
            $request->hora_inicio
        );

        if (!$validacionUnica) {

            $data = [
                'mensaje' => 'Ya existe un evento con ese ponente a la misma hora',
                'status' => 200,
            ];
            return response()->json($data, 200);
        }

        // Calcular hora finalización (inicio + 55 minutos)
        $hora_inicio = Carbon::createFromFormat('H:i', $request->hora_inicio);
        $hora_fin = $hora_inicio->addMinutes(55)->format('H:i');

        // Validar que no se solapen los eventos para este tipo de evento en el mismo día
        $validacionSolapamiento = Eventos::validarDistribucionEventos(
            $request->dia,
            $request->hora_inicio,
            $hora_fin,
            $request->tipo_evento
        );

        if (!$validacionSolapamiento) {
            $data = [
                'mensaje' => 'Ya hay un evento del mismo tipo programado a la misma hora',
                'status' => 200
            ];
            return response()->json($data, 200);
        }

        // Crear el evento
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

        $data = [
            'mensaje' => 'Evento creado correctamente',
            'evento' => $evento,
            'status' => 200,
        ];
        return response()->json($data, 200);
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
