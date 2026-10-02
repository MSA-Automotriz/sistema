<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next, string $role): Response
    {
        $user = $request->user();
        if (!$user || (!$user->hasRole($role) && !$user->hasRole('admin'))) {
            abort(403, 'No tienes permiso para acceder a esta página.');
        }

        return $next($request);
    }
}