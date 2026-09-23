<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckPermission
{
    /**
     * Handle an incoming request.
     * Verifica que el usuario tenga el permiso necesario
     * Los admins pueden acceder a todo sin restricciones
     */
    public function handle(Request $request, Closure $next, $permission): Response
    {
        if (!auth()->check()) {
            return redirect('login');
        }

        $user = auth()->user();

        // Los admins tienen acceso a todo
        if ($user->hasRole('admin')) {
            return $next($request);
        }

        // Verificar que el usuario tenga el permiso
        if ($user->hasPermission($permission)) {
            return $next($request);
        }

        // Si no tiene permiso, denegar acceso
        abort(403, "No tiene permisos para acceder a '{$permission}'");
    }
}
