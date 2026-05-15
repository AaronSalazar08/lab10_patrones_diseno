<?php

namespace App\Observers\Concretos;

use App\Models\RegistroPeso;
use App\Observers\IRegistroPesoObserver;
use Illuminate\Support\Facades\Log;

class AlertaSMS implements IRegistroPesoObserver
{
    public function onPesoRegistrado(RegistroPeso $registro): void
    {
        // En producción aquí iría la integración con Twilio u otro proveedor SMS.
        Log::info('[AlertaSMS] SMS enviado al propietario del animal ID: '
            .$registro->animal_id
            .' | Nuevo peso registrado: '.$registro->peso_kg.' kg'
        );
    }
}
