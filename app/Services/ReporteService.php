<?php

namespace App\Services;

use App\Factories\IRazaFactory;
use App\Repositories\IAnimalRepository;

class ReporteService
{
    public function __construct(
        private readonly IAnimalRepository $animalRepository,
        private readonly IRazaFactory $razaFactory
    ) {}

    /**
     * Genera reporte de todos los animales activos de un rancho.
     * Antes: Animal::where('rancho_id', $id)->with(...)->get() aquí directamente.
     * Ahora: el repositorio abstrae esa consulta.
     *
     * @return array<int, array<string, mixed>>
     */
    public function generarReporteRancho(int $ranchoId): array
    {
        $animales = $this->animalRepository->findAllByRancho($ranchoId);

        return array_map(function (array $animal) {
            try {
                $raza = $this->razaFactory->create($animal['raza']);
                $factor = $raza->getFactorCorreccionPeso();
            } catch (\InvalidArgumentException) {
                $factor = 1.0;
            }

            return [
                'arete' => $animal['arete'],
                'nombre' => $animal['nombre'] ?? 'Sin nombre',
                'raza' => $animal['raza'],
                'factor_correccion' => $factor,
                'estado' => $animal['estado'],
            ];
        }, $animales);
    }

    /**
     * Busca un animal específico por su arete y genera su ficha.
     *
     * @return array<string, mixed>|null
     */
    public function fichaAnimal(string $arete): ?array
    {
        $animal = $this->animalRepository->findByArete($arete);

        if (! $animal) {
            return null;
        }

        return [
            'arete' => $animal->arete,
            'nombre' => $animal->nombre ?? 'Sin nombre',
            'raza' => $animal->raza,
            'sexo' => $animal->sexo,
            'estado' => $animal->estado,
        ];
    }
}
