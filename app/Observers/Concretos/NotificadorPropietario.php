<?php

namespace App\Observers\Concretos;

use App\Models\RegistroPeso;
use App\Observers\IRegistroPesoObserver;
use Illuminate\Support\Facades\Log;

class NotificadorPropietario implements IRegistroPesoObserver
{
    public function onPesoRegistrado(RegistroPeso $registro): void
    {
        // En producción aquí iría: Mail::to($propietario->email)->send(...)
        Log::info('[NotificadorPropietario] Peso registrado para animal ID: '
            .$registro->animal_id
            .' | Peso: '.$registro->peso_kg.' kg'
            .' | Método: '.$registro->metodo_usado
        );
    }
}
