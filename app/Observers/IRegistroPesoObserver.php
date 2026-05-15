<?php

namespace App\Observers;

use App\Models\RegistroPeso;

interface IRegistroPesoObserver
{
    /**
     * Se ejecuta automáticamente cuando se registra un nuevo peso.
     * Cada observador concreto decide qué hacer con el registro.
     */
    public function onPesoRegistrado(RegistroPeso $registro): void;
}
