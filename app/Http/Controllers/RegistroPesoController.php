<?php

namespace App\Http\Controllers;

use App\Models\RegistroPeso;
use App\Observers\RegistroPesoSubject;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class RegistroPesoController extends Controller
{
    public function __construct(
        private readonly RegistroPesoSubject $subject
    ) {}

    /**
     * Registra un nuevo peso y notifica automáticamente a todos los observadores.
     * El controlador no sabe quiénes son los observadores ni cuántos son.
     */
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'animal_id' => 'required|integer',
            'peso_kg' => 'required|numeric|min:1',
            'metodo_usado' => 'required|in:yolov8,regresion,tabla',
            'confianza_porcentaje' => 'nullable|numeric|min:0|max:100',
            'foto_path' => 'nullable|string',
        ]);

        $registro = new RegistroPeso;
        $registro->animal_id = $request->input('animal_id');
        $registro->peso_kg = $request->input('peso_kg');
        $registro->metodo_usado = $request->input('metodo_usado');
        $registro->confianza_porcentaje = $request->input('confianza_porcentaje', 0);
        $registro->foto_path = $request->input('foto_path');
        $registro->fecha_registro = now();

        // Una sola línea reemplaza las 4 llamadas directas que había antes.
        // Los 4 observadores se ejecutan automáticamente.
        $this->subject->registrarPeso($registro);

        return response()->json([
            'mensaje' => 'Peso registrado. Observadores notificados automáticamente.',
            'registro_id' => $registro->id,
            'animal_id' => $registro->animal_id,
            'peso_kg' => $registro->peso_kg,
            'metodo_usado' => $registro->metodo_usado,
            'observadores_notificados' => $this->subject->totalObservadores(),
        ], 201);
    }
}
