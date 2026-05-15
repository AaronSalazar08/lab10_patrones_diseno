<?php

namespace App\Domain\Razas;

use App\Domain\Raza;

class Nelore extends Raza
{
    public function getNombre(): string
    {
        return 'Nelore';
    }

    public function getFactorCorreccionPeso(): float
    {
        return 1.02;
    }
}
