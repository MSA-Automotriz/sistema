<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Database\Eloquent\Relations\Relation;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register()
    {
        // Verificar si existe la clase antes de bindear
        if (class_exists(\App\Services\InventarioService::class)) {
            $this->app->bind(\App\Services\InventarioService::class, function ($app) {
                return new \App\Services\InventarioService();
            });
        }
    }

    /**
     * Bootstrap any application services.
     */
    public function boot()
    {
        Relation::morphMap([
            'parte' => \App\Models\Parte::class,
            'vehiculo' => \App\Models\Vehiculo::class,
            'devolucion_proveedor' => \App\Models\DevolucionProveedor::class,
        ]);
    }
}
