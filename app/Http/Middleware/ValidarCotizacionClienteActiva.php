<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\Cotizacion;
use App\Models\EstadoCotizacion;
use App\Models\ReglaVencimientoCotizacion;

class ValidarCotizacionClienteActiva
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Solo aplicar en la creación de cotizaciones
        if ($request->isMethod('post') && $request->routeIs('admin.ventas.cotizaciones.store')) {
            $clienteId = $request->input('cliente_id');
            $userId = auth()->id();
            
            if (!$clienteId) {
                return $next($request);
            }
            
            // Verificar si el cliente tiene cotizaciones activas
            $cotizacionesActivas = $this->verificarCotizacionesActivas($clienteId, $userId);
            
            if ($cotizacionesActivas->isNotEmpty()) {
                $mensajes = [];
                $cotizacionesReasignables = collect();
                
                foreach ($cotizacionesActivas as $cotizacion) {
                    if ($cotizacion->user_id == $userId) {
                        // El mismo vendedor ya tiene una cotización activa
                        $mensajes[] = "Ya tienes una cotización activa para este cliente (#{$cotizacion->codigo}) creada el " . 
                                    $cotizacion->created_at->format('d/m/Y H:i');
                    } else {
                        // Otro vendedor tiene una cotización activa
                        if ($cotizacion->reasignable) {
                            $cotizacionesReasignables->push($cotizacion);
                            $mensajes[] = "El cliente tiene una cotización vencida (#{$cotizacion->codigo}) del vendedor {$cotizacion->usuario->name} que puede ser reasignada.";
                        } else {
                            $vendedor = $cotizacion->usuario->name ?? 'Vendedor no especificado';
                            $mensajes[] = "El cliente tiene una cotización activa (#{$cotizacion->codigo}) asignada al vendedor {$vendedor}.";
                        }
                    }
                }
                
                // Si hay cotizaciones reasignables, ofrecemos la opción
                if ($cotizacionesReasignables->isNotEmpty() && $request->input('confirmar_reasignacion') !== '1') {
                    return redirect()->back()
                        ->withInput()
                        ->with('cotizaciones_reasignables', $cotizacionesReasignables)
                        ->with('warning', 'Se encontraron cotizaciones que pueden ser reasignadas. ¿Deseas continuar?')
                        ->with('mensajes_cotizaciones', $mensajes);
                }
                
                // Si hay conflictos no resolubles
                if ($cotizacionesReasignables->isEmpty()) {
                    return redirect()->back()
                        ->withInput()
                        ->withErrors(['cliente_id' => implode(' ', $mensajes)]);
                }
                
                // Si el usuario confirma la reasignación
                if ($request->input('confirmar_reasignacion') === '1' && $cotizacionesReasignables->isNotEmpty()) {
                    foreach ($cotizacionesReasignables as $cotizacion) {
                        $cotizacion->reasignar($userId);
                    }
                }
            }
        }
        
        return $next($request);
    }
    
    /**
     * Verifica las cotizaciones activas del cliente
     */
    private function verificarCotizacionesActivas($clienteId, $userId)
    {
        // Estados que se consideran "activos" (no finalizados)
        $estadosActivos = EstadoCotizacion::whereNotIn('nombre', [
            'Cerrado Perdido',
            'Cerrado Ganado', 
            'Convertida',
            'Cancelada',
            'Rechazada'
        ])->pluck('id');
        
        return Cotizacion::where('cliente_id', $clienteId)
            ->whereIn('estado_id', $estadosActivos)
            ->with(['usuario', 'estado', 'reglaVencimiento'])
            ->orderBy('created_at', 'desc')
            ->get();
    }
}
