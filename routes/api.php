<?php

use App\Http\Controllers\AnimalController;
use Illuminate\Support\Facades\Route;

// Punto de creación #1 refactorizado — usa Factory vía inyección
Route::post('/animales', [AnimalController::class, 'store']);

// Lista las razas disponibles (demuestra extensibilidad del patrón)
Route::get('/razas', [AnimalController::class, 'razasDisponibles']);
