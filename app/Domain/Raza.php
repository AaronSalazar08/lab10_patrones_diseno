<?php

namespace App\Domain;

abstract class Raza
{
    abstract public function getNombre(): string;

    abstract public function getFactorCorreccionPeso(): float;

    public function describir(): string
    {
        return sprintf(
            'Raza: %s | Factor de corrección: %.2f',
            $this->getNombre(),
            $this->getFactorCorreccionPeso()
        );
    }
}
