<?php

namespace App\Services;

use App\Factories\IRazaFactory;

class ReporteService
{
    public function __construct(
        private readonly IRazaFactory $razaFactory
    ) {}

    /**
     * Genera un reporte básico del animal usando la raza correcta.
     * Antes: new Nelore() o new Brahman() hardcodeado aquí.
     * Ahora: la factory resuelve la clase en tiempo de ejecución.
     *
     * @return array<string, mixed>
     */
    public function generarReporteAnimal(string $nombreRaza, float $pesoFotoKg): array
    {
        $raza = $this->razaFactory->create($nombreRaza);
        $pesoEstimado = round($pesoFotoKg * $raza->getFactorCorreccionPeso(), 2);

        return [
            'raza' => $raza->getNombre(),
            'descripcion' => $raza->describir(),
            'peso_foto_kg' => $pesoFotoKg,
            'peso_estimado_kg' => $pesoEstimado,
            'factor_aplicado' => $raza->getFactorCorreccionPeso(),
        ];
    }
}
