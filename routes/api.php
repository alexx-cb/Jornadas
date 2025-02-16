<?php

use App\Http\Controllers\Api\PonentesController;
use App\Http\Controllers\Api\UsuarioCaracteristicasController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::get('/usuarioCaracteristicas', [UsuarioCaracteristicasController::class, 'index']);

Route::get('/ponentes', [PonentesController::class, 'index']);
Route::post('/ponentes', [PonentesController::class, 'store']);

Route::delete('/ponentes/{id}', [PonentesController::class, 'destroy']);
