<?php

use App\Http\Controllers\AnimalController;
use App\Http\Controllers\RanchoController;
use App\Http\Controllers\RegistroPesoController;
use Illuminate\Support\Facades\Route;

// Patrón Factory
Route::post('/animales', [AnimalController::class, 'store']);
Route::get('/razas', [AnimalController::class, 'razasDisponibles']);

// Patrón Repository
Route::get('/ranchos/{ranchoId}/animales', [RanchoController::class, 'animales']);
Route::get('/animales/arete/{arete}', [RanchoController::class, 'buscarPorArete']);
Route::post('/ranchos/animales', [RanchoController::class, 'store']);
Route::delete('/animales/{id}', [RanchoController::class, 'destroy']);

// Patrón Observer
Route::post('/registros-peso', [RegistroPesoController::class, 'store']);
