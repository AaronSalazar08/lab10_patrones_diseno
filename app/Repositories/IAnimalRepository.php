<?php

namespace App\Repositories;

use App\Models\Animal;

interface IAnimalRepository
{
    /**
     * Busca un animal por su número de arete SENASA.
     * Retorna null si no existe.
     */
    public function findByArete(string $arete): ?Animal;

    /**
     * Retorna todos los animales activos de un rancho.
     */
    public function findAllByRancho(int $ranchoId): array;

    /**
     * Persiste un animal nuevo o actualiza uno existente.
     */
    public function save(Animal $animal): void;

    /**
     * Elimina un animal por su ID.
     */
    public function delete(int $id): void;

    /**
     * Retorna todos los animales activos del sistema.
     */
    public function findAllActivos(): array;
}
