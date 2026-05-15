<?php

namespace App\Domain;

class ResultadoEstimacion
{
    public function __construct(
        public readonly float $pesoKg,
        public readonly float $confianzaPorcentaje,
        public readonly string $metodoUsado
    ) {}

    /**
     * Representación legible del resultado para logs o respuestas API.
     */
    public function describir(): string
    {
        return sprintf(
            'Método: %s | Peso estimado: %.2f kg | Confianza: %.1f%%',
            $this->metodoUsado,
            $this->pesoKg,
            $this->confianzaPorcentaje
        );
    }

    /**
     * Indica si el resultado tiene una confianza aceptable (>=70%).
     */
    public function esConfiable(): bool
    {
        return $this->confianzaPorcentaje >= 70.0;
    }
}
