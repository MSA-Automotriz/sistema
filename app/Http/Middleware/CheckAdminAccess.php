<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckAdminAccess
{
    /**
     * Handle an incoming request.
     * Bloquea el acceso al admin a usuarios con rol 'cliente' o sin roles
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!auth()->check()) {
            return redirect('login');
        }

        $user = auth()->user();

        // Si el usuario no tiene ningún rol asignado, denegar acceso
        if ($user->roles()->count() === 0) {
            abort(403, 'No tiene un rol asignado');
        }

        // Si el usuario solo tiene rol 'cliente', denegar acceso al admin
        if ($user->hasRole('cliente') && !$user->roles()->where('name', '!=', 'cliente')->exists()) {
            abort(403, 'Los clientes no pueden acceder al panel de administración');
        }

        return $next($request);
    }
}
