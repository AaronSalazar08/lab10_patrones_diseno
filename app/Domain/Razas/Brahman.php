<?php

namespace App\Domain\Razas;

use App\Domain\Raza;

class Brahman extends Raza
{
    public function getNombre(): string
    {
        return 'Brahman';
    }

    public function getFactorCorreccionPeso(): float
    {
        return 1.05;
    }
}
