<?php

namespace App\Factories;

use App\Domain\Raza;

interface IRazaFactory
{
    /**
     * Crea una instancia de Raza según el nombre proporcionado.
     * El código cliente depende de esta interfaz, nunca de clases concretas.
     *
     * @throws \InvalidArgumentException si la raza no está registrada
     */
    public function create(string $nombreRaza): Raza;
}
