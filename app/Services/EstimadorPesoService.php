<?php

namespace App\Services;

use App\Domain\ResultadoEstimacion;
use App\Strategies\Concretas\AlgoritmoTablaReferencia;
use App\Strategies\IAlgoritmoEstimacion;
use Illuminate\Support\Facades\Log;

class EstimadorPesoService
{
    public function __construct(
        private IAlgoritmoEstimacion $algoritmo
    ) {}

    /**
     * Estima el peso del animal usando la estrategia inyectada.
     * SIN if-else. El algoritmo concreto es completamente transparente.
     *
     * Si el algoritmo inyectado no está disponible, hace fallback
     * automático a AlgoritmoTablaReferencia.
     *
     * @param  array<string, mixed>  $datosEntrada
     */
    public function estimar(array $datosEntrada): ResultadoEstimacion
    {
        if (! $this->algoritmo->estaDisponible()) {
            Log::warning('[EstimadorPesoService] Algoritmo principal no disponible. '
                .'Usando fallback: AlgoritmoTablaReferencia.');

            return (new AlgoritmoTablaReferencia)->ejecutar($datosEntrada);
        }

        return $this->algoritmo->ejecutar($datosEntrada);
    }

    /**
     * Cambia la estrategia en tiempo de ejecución.
     * Demuestra que el cambio de algoritmo no requiere modificar este servicio.
     */
    public function cambiarAlgoritmo(IAlgoritmoEstimacion $nuevoAlgoritmo): void
    {
        $this->algoritmo = $nuevoAlgoritmo;
    }
}
