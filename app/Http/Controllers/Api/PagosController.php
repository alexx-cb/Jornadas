<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Pagos;
use App\Http\Requests\PagosRequest;

class PagosController extends Controller
{
    public function index()
    {
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

    public function store(PagosRequest $request)
    {
        $pago = Pagos::create($request->validated());

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

    public function show($id)
    {
        $pago = Pagos::find($id);

        if(!$pago){
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
