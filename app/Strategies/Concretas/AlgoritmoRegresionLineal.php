<?php

namespace App\Strategies\Concretas;

use App\Domain\ResultadoEstimacion;
use App\Strategies\IAlgoritmoEstimacion;
use Illuminate\Support\Facades\Log;

class AlgoritmoRegresionLineal implements IAlgoritmoEstimacion
{
    /**
     * Coeficientes de regresión por raza (simplificados para demostración).
     * En producción vendrían de un modelo entrenado con datos históricos.
     *
     * @var array<string, array<string, float>>
     */
    private array $coeficientesPorRaza = [
        'brahman' => ['pendiente' => 1.04, 'intercepto' => 12.5],
        'nelore' => ['pendiente' => 1.02, 'intercepto' => 8.3],
        'angus' => ['pendiente' => 1.07, 'intercepto' => 15.2],
    ];

    public function ejecutar(array $datosEntrada): ResultadoEstimacion
    {
        $pesoReferencia = $datosEntrada['peso_referencia_kg'] ?? 350.0;
        $raza = strtolower($datosEntrada['raza'] ?? 'brahman');

        $coef = $this->coeficientesPorRaza[$raza]
            ?? $this->coeficientesPorRaza['brahman'];

        // y = pendiente * x + intercepto
        $pesoEstimado = round(
            ($coef['pendiente'] * $pesoReferencia) + $coef['intercepto'],
            2
        );
        $confianza = 75.0;

        Log::info('[AlgoritmoRegresionLineal] Estimación completada', [
            'raza' => $raza,
            'peso_estimado' => $pesoEstimado,
            'confianza' => $confianza,
        ]);

        return new ResultadoEstimacion(
            pesoKg: $pesoEstimado,
            confianzaPorcentaje: $confianza,
            metodoUsado: 'regresion'
        );
    }

    public function estaDisponible(): bool
    {
        return true;
    }
}
