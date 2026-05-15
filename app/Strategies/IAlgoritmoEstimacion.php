<?php

namespace App\Strategies;

use App\Domain\ResultadoEstimacion;

interface IAlgoritmoEstimacion
{
    /**
     * Ejecuta el algoritmo de estimación con los datos de entrada.
     *
     * @param  array<string, mixed>  $datosEntrada  Datos necesarios para la estimación:
     *                                              - 'raza' (string): raza del animal
     *                                              - 'peso_referencia_kg' (float): peso base de referencia
     *                                              - 'foto_path' (string|null): ruta de la fotografía (para YOLOv8)
     * @return ResultadoEstimacion Value object inmutable con el resultado
     */
    public function ejecutar(array $datosEntrada): ResultadoEstimacion;

    /**
     * Indica si el algoritmo está disponible en este momento.
     * Usado para el fallback automático.
     */
    public function estaDisponible(): bool;
}
