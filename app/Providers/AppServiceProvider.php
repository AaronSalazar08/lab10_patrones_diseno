<?php

namespace App\Providers;

use App\Factories\IRazaFactory;
use App\Factories\RazaFactory;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        // Singleton: una sola instancia de RazaFactory en toda la aplicación.
        // Todo el código que pida IRazaFactory recibirá siempre la misma instancia.
        $this->app->singleton(IRazaFactory::class, RazaFactory::class);
    }

    public function boot(): void
    {
        //
    }
}
