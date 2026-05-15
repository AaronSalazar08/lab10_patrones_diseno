<?php

namespace App\Repositories;

use App\Models\Animal;

class EloquentAnimalRepository implements IAnimalRepository
{
    public function findByArete(string $arete): ?Animal
    {
        return Animal::where('arete', $arete)->first();
    }

    public function findAllByRancho(int $ranchoId): array
    {
        // Este es el punto que antes estaba repetido en ReporteService,
        // EstimadorService y DashboardController. Ahora vive aquí únicamente.
        return Animal::where('rancho_id', $ranchoId)
            ->where('estado', 'activo')
            ->get()
            ->toArray();
    }

    public function save(Animal $animal): void
    {
        $animal->save();
    }

    public function delete(int $id): void
    {
        Animal::destroy($id);
    }

    public function findAllActivos(): array
    {
        return Animal::where('estado', 'activo')->get()->toArray();
    }
}
