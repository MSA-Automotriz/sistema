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

        // Registrar Gate::before para compatibilidad con @can y Gate checks
        \Illuminate\Support\Facades\Gate::before(function ($user, $ability) {
            if ($user->hasRole('admin')) {
                return true;
            }
            return $user->hasPermission($ability) ? true : null;
        });

        // Directivas Blade personalizadas
        \Illuminate\Support\Facades\Blade::directive('permission', function ($permission) {
            return "<?php if(auth()->check() && auth()->user()->hasPermission({$permission})): ?>";
        });

        \Illuminate\Support\Facades\Blade::directive('endpermission', function () {
            return "<?php endif; ?>";
        });

        \Illuminate\Support\Facades\Blade::directive('role', function ($role) {
            return "<?php if(auth()->check() && auth()->user()->hasRole({$role})): ?>";
        });

        \Illuminate\Support\Facades\Blade::directive('endrole', function () {
            return "<?php endif; ?>";
        });
    }
}
