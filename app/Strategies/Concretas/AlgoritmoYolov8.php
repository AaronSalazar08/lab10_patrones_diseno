<?php

namespace App\Strategies\Concretas;

use App\Domain\ResultadoEstimacion;
use App\Strategies\IAlgoritmoEstimacion;
use Illuminate\Support\Facades\Log;

class AlgoritmoYolov8 implements IAlgoritmoEstimacion
{
    private string $urlServicio;

    private bool $servicioActivo;

    public function __construct(
        string $urlServicio = 'http://ml-service:5000/estimar',
        bool $servicioActivo = true
    ) {
        $this->urlServicio = $urlServicio;
        $this->servicioActivo = $servicioActivo;
    }

    public function ejecutar(array $datosEntrada): ResultadoEstimacion
    {
        $pesoReferencia = $datosEntrada['peso_referencia_kg'] ?? 350.0;

        // Simulación de la llamada HTTP al microservicio YOLOv8.
        // En producción:
        // $response = Http::timeout(15)->post($this->urlServicio, $datosEntrada);
        // $pesoEstimado = $response->json('peso_kg');
        // $confianza    = $response->json('confianza');

        $pesoEstimado = round($pesoReferencia * 1.03 + rand(-5, 5), 2);
        $confianza = round(rand(82, 96) + (rand(0, 9) / 10), 1);

        Log::info('[AlgoritmoYolov8] Estimación completada', [
            'peso_estimado' => $pesoEstimado,
            'confianza' => $confianza,
        ]);

        return new ResultadoEstimacion(
            pesoKg: $pesoEstimado,
            confianzaPorcentaje: $confianza,
            metodoUsado: 'yolov8'
        );
    }

    public function estaDisponible(): bool
    {
        return $this->servicioActivo;
    }
}
