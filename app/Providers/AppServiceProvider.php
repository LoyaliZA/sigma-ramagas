<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\URL;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Lógica Híbrida para Producción
        if ($this->app->environment('production')) {
            
            $host = request()->getHost();

            // EXPRESIÓN REGULAR: Verifica si es IP (Local 192... o Tailscale 100...)
            if (preg_match('/^(\d{1,3}\.){3}\d{1,3}$/', $host)) {
                // CASO IP: Forzamos HTTP y desactivamos cookies 'secure'
                URL::forceScheme('http');
                config(['session.secure' => false]);
            } else {
                // CASO DOMINIO: Forzamos HTTPS y activamos cookies 'secure'
                URL::forceScheme('https');
                config(['session.secure' => true]);
            }
        }
    }
}