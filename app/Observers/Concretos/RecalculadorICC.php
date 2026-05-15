<?php

namespace App\Observers\Concretos;

use App\Models\RegistroPeso;
use App\Observers\IRegistroPesoObserver;
use Illuminate\Support\Facades\Log;

class RecalculadorICC implements IRegistroPesoObserver
{
    public function onPesoRegistrado(RegistroPeso $registro): void
    {
        $icc = $this->calcularICC($registro->peso_kg);

        Log::info('[RecalculadorICC] ICC recalculado para animal ID: '
            .$registro->animal_id
            .' | Peso: '.$registro->peso_kg.' kg'
            .' | ICC estimado: '.$icc
        );
    }

    private function calcularICC(float $pesoKg): float
    {
        // Escala 1-5: animales entre 300-500kg en condición ideal (3.0)
        $icc = round(($pesoKg / 400) * 3, 2);

        return min(5.0, max(1.0, $icc));
    }
}
