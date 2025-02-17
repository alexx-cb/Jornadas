<?php

use App\Http\Controllers\ProfileController;
use App\Http\Middleware\RoleMiddleware;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::get('/ponentes', function () {
    return view('ponentes.show');
})->name('ponentes.show');

Route::middleware(['auth', RoleMiddleware::class.':admin'])->get('/ponentes/crear', function () {
    return view('ponentes.crear');

})->name('ponentes.crear');


Route::middleware(['auth', RoleMiddleware::class. ':admin'])->get('/ponentes/{id}/editar', function ($id) {
    return view('ponentes.editar', ['id' => $id]);

})->name('ponentes.editar');

Route::get('/eventos', function () {
    return view('eventos.show');
})->name('eventos.show');


Route::middleware(['auth', RoleMiddleware::class. ':admin'])->get('/eventos/crear', function () {
    return view('eventos.crear');
})->name('eventos.crear');

require __DIR__.'/auth.php';
