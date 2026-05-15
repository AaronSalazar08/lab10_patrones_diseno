<?php

namespace App\Strategies\Concretas;

use App\Domain\ResultadoEstimacion;
use App\Strategies\IAlgoritmoEstimacion;
use Illuminate\Support\Facades\Log;

class AlgoritmoTablaReferencia implements IAlgoritmoEstimacion
{
    /**
     * Tabla de pesos promedio por raza en kg (datos reales de ganadería CR).
     *
     * @var array<string, array<string, int>>
     */
    private array $tablaPesos = [
        'brahman' => ['min' => 380, 'max' => 550, 'promedio' => 450],
        'nelore' => ['min' => 350, 'max' => 500, 'promedio' => 420],
        'angus' => ['min' => 400, 'max' => 600, 'promedio' => 480],
    ];

    public function ejecutar(array $datosEntrada): ResultadoEstimacion
    {
        $raza = strtolower($datosEntrada['raza'] ?? 'brahman');
        $pesoReferencia = $datosEntrada['peso_referencia_kg'] ?? null;

        $entrada = $this->tablaPesos[$raza] ?? $this->tablaPesos['brahman'];

        // Si hay peso de referencia, ajusta dentro del rango de la tabla.
        // Si no hay, usa el promedio de la raza.
        if ($pesoReferencia !== null) {
            $pesoEstimado = round(
                max($entrada['min'], min($entrada['max'], $pesoReferencia)),
                2
            );
        } else {
            $pesoEstimado = $entrada['promedio'];
        }

        $confianza = 60.0;

        Log::info('[AlgoritmoTablaReferencia] Estimación completada', [
            'raza' => $raza,
            'peso_estimado' => $pesoEstimado,
            'confianza' => $confianza,
        ]);

        return new ResultadoEstimacion(
            pesoKg: $pesoEstimado,
            confianzaPorcentaje: $confianza,
            metodoUsado: 'tabla'
        );
    }

    public function estaDisponible(): bool
    {
        return true;
    }
}
