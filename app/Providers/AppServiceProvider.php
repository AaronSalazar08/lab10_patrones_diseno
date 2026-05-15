<?php

namespace App\Providers;

use App\Factories\IRazaFactory;
use App\Factories\RazaFactory;
use App\Observers\Concretos\AlertaSMS;
use App\Observers\Concretos\NotificadorPropietario;
use App\Observers\Concretos\RecalculadorICC;
use App\Observers\Concretos\WebhookSenasa;
use App\Observers\RegistroPesoSubject;
use App\Repositories\EloquentAnimalRepository;
use App\Repositories\IAnimalRepository;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        // Patrón Factory — ya existía
        $this->app->singleton(IRazaFactory::class, RazaFactory::class);

        // Patrón Repository — ya existía
        $this->app->bind(IAnimalRepository::class, EloquentAnimalRepository::class);

        // Patrón Observer — nuevo
        // Singleton para que todos compartan el mismo subject con sus observadores
        $this->app->singleton(RegistroPesoSubject::class, function () {
            $subject = new RegistroPesoSubject;

            $subject->suscribir(new NotificadorPropietario);
            $subject->suscribir(new RecalculadorICC);
            $subject->suscribir(new WebhookSenasa);

            // Cuarto observador: AlertaSMS agregado SIN tocar RegistroPesoSubject
            $subject->suscribir(new AlertaSMS);

            return $subject;
        });
    }

    public function boot(): void
    {
        //
    }
}
