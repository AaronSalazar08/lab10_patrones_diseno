<?php

namespace App\Http\Controllers;

use App\Services\EstimadorPesoService;
use App\Strategies\Concretas\AlgoritmoYolov8;
use App\Strategies\EstimadorFactory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class EstimadorController extends Controller
{
    public function __construct(
        private readonly EstimadorPesoService $estimador,
        private readonly EstimadorFactory $factory
    ) {}

    /**
     * Estima el peso usando el algoritmo solicitado.
     * Demuestra cambio de estrategia en tiempo de ejecución.
     */
    public function estimar(Request $request): JsonResponse
    {
        $request->validate([
            'raza' => 'required|string',
            'peso_referencia_kg' => 'required|numeric|min:1',
            'metodo' => 'required|in:yolov8,regresion,tabla',
        ]);

        $datosEntrada = [
            'raza' => $request->input('raza'),
            'peso_referencia_kg' => (float) $request->input('peso_referencia_kg'),
            'foto_path' => $request->input('foto_path'),
        ];

        // Cambio de estrategia en tiempo de ejecución según el método pedido.
        // SIN if-else: la factory resuelve la clase, el servicio la ejecuta.
        try {
            $algoritmo = $this->factory->crear($request->input('metodo'));
            $this->estimador->cambiarAlgoritmo($algoritmo);
        } catch (\InvalidArgumentException $e) {
            return response()->json(['error' => $e->getMessage()], 422);
        }

        $resultado = $this->estimador->estimar($datosEntrada);

        return response()->json([
            'raza' => $datosEntrada['raza'],
            'peso_referencia_kg' => $datosEntrada['peso_referencia_kg'],
            'peso_estimado_kg' => $resultado->pesoKg,
            'confianza_porcentaje' => $resultado->confianzaPorcentaje,
            'metodo_usado' => $resultado->metodoUsado,
            'es_confiable' => $resultado->esConfiable(),
            'descripcion' => $resultado->describir(),
        ]);
    }

    /**
     * Demuestra el fallback automático cuando YOLOv8 no está disponible.
     */
    public function estimarConFallback(Request $request): JsonResponse
    {
        $request->validate([
            'raza' => 'required|string',
            'peso_referencia_kg' => 'required|numeric|min:1',
        ]);

        $datosEntrada = [
            'raza' => $request->input('raza'),
            'peso_referencia_kg' => (float) $request->input('peso_referencia_kg'),
        ];

        // Simula YOLOv8 caído: servicioActivo = false
        $yolov8Caido = new AlgoritmoYolov8(
            urlServicio: 'http://ml-service:5000/estimar',
            servicioActivo: false
        );

        $this->estimador->cambiarAlgoritmo($yolov8Caido);

        // EstimadorPesoService detecta que no está disponible y usa tabla automáticamente
        $resultado = $this->estimador->estimar($datosEntrada);

        return response()->json([
            'mensaje' => 'YOLOv8 no disponible. Fallback activado automáticamente.',
            'metodo_usado' => $resultado->metodoUsado,
            'peso_estimado_kg' => $resultado->pesoKg,
            'confianza_porcentaje' => $resultado->confianzaPorcentaje,
            'es_confiable' => $resultado->esConfiable(),
        ]);
    }
}
