<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\URL;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
        // Aquí puedes registrar bindings, macros o servicios
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Fix para longText en MySQL versiones antiguas
        Schema::defaultStringLength(125); // o 191 si prefieres

        // // Forzar HTTPS en producción (opcional)
        // if ($this->app->environment('production')) {
        //     URL::forceScheme('https');
        // }

        // Otras configuraciones globales aquí
    }
}
