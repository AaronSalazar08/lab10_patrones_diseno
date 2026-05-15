<?php

namespace App\Domain\Razas;

use App\Domain\Raza;

class Angus extends Raza
{
    public function getNombre(): string
    {
        return 'Angus';
    }

    public function getFactorCorreccionPeso(): float
    {
        return 1.08;
    }
}
