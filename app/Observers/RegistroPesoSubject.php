<?php

namespace App\Observers;

use App\Models\RegistroPeso;

class RegistroPesoSubject
{
    /**
     * Lista de observadores suscritos.
     * El subject no sabe qué tipo concreto son, solo que implementan
     * IRegistroPesoObserver.
     */
    private array $observadores = [];

    /**
     * Suscribe un observador a los eventos de registro de peso.
     * Agregar AlertaSMS no requiere modificar esta clase.
     */
    public function suscribir(IRegistroPesoObserver $observador): void
    {
        $this->observadores[] = $observador;
    }

    /**
     * Desuscribe un observador de los eventos.
     */
    public function desuscribir(IRegistroPesoObserver $observador): void
    {
        $this->observadores = array_filter(
            $this->observadores,
            fn ($o) => $o !== $observador
        );
    }

    /**
     * Punto de entrada principal: guarda el registro y notifica a todos.
     * El controlador solo llama este método. Ya no llama a cada subsistema.
     */
    public function registrarPeso(RegistroPeso $registro): void
    {
        $registro->save();
        $this->notificar($registro);
    }

    /**
     * Privado: recorre la lista y avisa a cada observador.
     * El subject no sabe qué hará cada uno con la notificación.
     */
    private function notificar(RegistroPeso $registro): void
    {
        foreach ($this->observadores as $observador) {
            $observador->onPesoRegistrado($registro);
        }
    }

    /**
     * Retorna la cantidad de observadores suscritos.
     * Útil para pruebas y diagnóstico.
     */
    public function totalObservadores(): int
    {
        return count($this->observadores);
    }
}
