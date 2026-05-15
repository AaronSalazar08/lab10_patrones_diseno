<?php

namespace App\Repositories;

use App\Models\Animal;

class InMemoryAnimalRepository implements IAnimalRepository
{
    private array $almacen = [];

    private int $nextId = 1;

    public function findByArete(string $arete): ?Animal
    {
        foreach ($this->almacen as $animal) {
            if ($animal->arete === $arete) {
                return $animal;
            }
        }

        return null;
    }

    public function findAllByRancho(int $ranchoId): array
    {
        return array_values(array_filter(
            $this->almacen,
            fn (Animal $a) => $a->rancho_id === $ranchoId && $a->estado === 'activo'
        ));
    }

    public function save(Animal $animal): void
    {
        if (! $animal->id) {
            $animal->id = $this->nextId++;
        }
        $this->almacen[$animal->id] = $animal;
    }

    public function delete(int $id): void
    {
        unset($this->almacen[$id]);
    }

    public function findAllActivos(): array
    {
        return array_values(array_filter(
            $this->almacen,
            fn (Animal $a) => $a->estado === 'activo'
        ));
    }

    /**
     * Limpia el almacén entre pruebas.
     */
    public function limpiar(): void
    {
        $this->almacen = [];
        $this->nextId = 1;
    }
}
