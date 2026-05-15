<?php

namespace App\Factories;

use App\Domain\Raza;
use App\Domain\Razas\Angus;
use App\Domain\Razas\Brahman;
use App\Domain\Razas\Nelore;
use InvalidArgumentException;

class RazaFactory implements IRazaFactory
{
    /**
     * Mapa de razas registradas en el sistema.
     * Para agregar una nueva raza (ej: PardoSuizo) basta con añadir una línea aquí.
     * NO es necesario modificar ningún controlador ni servicio existente.
     *
     * @var array<string, class-string<Raza>>
     */
    private array $mapa = [
        'brahman' => Brahman::class,
        'nelore' => Nelore::class,
        'angus' => Angus::class,
        // 'pardosuizo' => PardoSuizo::class,
    ];

    public function create(string $nombreRaza): Raza
    {
        $clave = strtolower(trim($nombreRaza));

        if (! array_key_exists($clave, $this->mapa)) {
            throw new InvalidArgumentException(
                "Raza '{$nombreRaza}' no está registrada en el sistema. ".
                'Razas disponibles: '.implode(', ', array_keys($this->mapa))
            );
        }

        $clase = $this->mapa[$clave];

        return new $clase();
    }

    /**
     * Retorna la lista de razas disponibles en el sistema.
     * Útil para validaciones en formularios o documentación de la API.
     *
     * @return array<string>
     */
    public function getRazasDisponibles(): array
    {
        return array_keys($this->mapa);
    }
}
