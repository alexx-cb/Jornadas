<?php

use App\Http\Controllers\Api\EstudiantesController;
use App\Http\Controllers\Api\EventosController;
use App\Http\Controllers\Api\PonentesController;
use App\Http\Controllers\Api\UsuarioCaracteristicasController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::get('/usuarioCaracteristicas', [UsuarioCaracteristicasController::class, 'index']);
Route::patch('/usuarioCaracteristicas/{id}', [UsuarioCaracteristicasController::class, 'updatePartial']);

Route::get('/ponentes', [PonentesController::class, 'index']);
Route::post('/ponentes', [PonentesController::class, 'store']);
Route::get('/ponentes/{id}', [PonentesController::class, 'show']);
Route::put('/ponentes/{id}', [PonentesController::class, 'update']);
Route::delete('/ponentes/{id}', [PonentesController::class, 'destroy']);

Route::get('/eventos', [EventosController::class, 'index']);
Route::post('/eventos', [EventosController::class, 'store']);
Route::delete('/eventos/{id}', [EventosController::class, 'destroy']);


Route::get('/estudiantes', [EstudiantesController::class, 'index']);
Route::post('/estudiantes', [EstudiantesController::class, 'store']);
Route::delete('/estudiantes/{id}', [EstudiantesController::class, 'destroy']);
Route::put('/estudiantes/{id}', [EstudiantesController::class, 'update']);

