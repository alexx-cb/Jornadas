<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Pagos;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PagosController extends Controller
{
    public function index(){
        $pagos = Pagos::all();

        if($pagos->isEmpty()){
            $data = [
                'mensaje' => 'No hay pagos registrados',
                'status' => 200,
            ];
            return response()->json($data, 200);
        }

        $data = [
            'pagos' => $pagos,
            'status' => 200,
        ];
        return response()->json($data, 200);
    }

    public function store(Request $request){

        $validator = Validator::make($request->all(), [
            'user_id' => 'required|integer|exists:users,id|unique:pagos,user_id',
            'tipo_pago' => 'required|string|in:Presencial,Virtual,Gratuito',
            'cantidad' => 'required|numeric',
            'fecha_pago' => 'required|date',
            'estado' =>  'required|string|in:Pagado,Pendiente',
        ]);

        if($validator->fails()){
            $data = [
                'mensaje' => 'Error en la validacion',
                'error' => $validator->errors(),
                'status' => 500,
            ];
            return response()->json($data, 500);
        }

        $pago = Pagos::create([
            'user_id' => $request->input('user_id'),
            'tipo_pago' => $request->input('tipo_pago'),
            'cantidad' => $request->input('cantidad'),
            'fecha_pago' => $request->input('fecha_pago'),
            'estado' => $request->input('estado'),
        ]);

        if(!$pago){
            $data = [
                'mensaje' => 'No se pudo registrar el pago',
                'status' => 500,
            ];
            return response()->json($data, 500);
        }
        $data = [
            'mensaje' => 'Pago registrado',
            'pago' => $pago,
            'status' => 200,
        ];
        return response()->json($data, 200);
    }

    public function show($id){
        $pago = Pagos::find($id);

        if($pago->isEmpty()){
            $data = [
                'mensaje' => 'No se ha encontrado el pago',
                'status' => 400,
            ];
            return response()->json($data, 400);
        }
        $data = [
            'pago' => $pago,
            'status' => 200,
        ];
        return response()->json($data, 200);
    }
}
