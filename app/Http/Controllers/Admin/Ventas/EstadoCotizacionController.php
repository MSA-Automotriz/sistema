<?php

namespace App\Http\Controllers\Admin\Ventas;

use App\Http\Controllers\Controller;
use App\Models\Cotizacion;
use App\Models\SeguimientoCotizacion;
use App\Models\EstadoCotizacion;
use App\Models\HistorialCotizacion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class EstadoCotizacionController extends Controller
{
    /**
     * Cambiar el estado de la cotización
     */
    public function cambiarEstado(Request $request, $cotizacionId)
    {
        $request->validate([
            'estado_id' => 'required|exists:estados_cotizacion,id',
            'comentario' => 'required|string|max:1000',
            'cotizacion_enviada' => 'nullable|in:si,no',
            'metodo_pago' => 'nullable|string',
            'solicitud_credito' => 'nullable|in:si,no',
            'estado_credito' => 'nullable|string',
            'motivo_rechazo' => 'nullable|string',
            'fecha_cierre' => 'nullable|date',
            'monto_venta' => 'nullable|numeric',
            'fecha_aceptacion' => 'nullable|date',
            'motivo_rechazo_estado' => 'nullable|string',
        ]);

        $cotizacion = Cotizacion::findOrFail($cotizacionId);
        $estadoAnteriorId = $cotizacion->estado_id;
        $estadoNuevoId = $request->estado_id;

        // Validar que la cotización tenga un estado asignado
        if (!$estadoAnteriorId) {
            return response()->json(['error' => 'La cotización no tiene un estado asignado'], 400);
        }

        // Validar que el estado nuevo sea diferente al actual
        if ($estadoAnteriorId == $estadoNuevoId) {
            return response()->json(['error' => 'El estado seleccionado es el mismo que el actual'], 400);
        }

        $estadoNuevo = EstadoCotizacion::find($estadoNuevoId);
        if (!$estadoNuevo) {
            return response()->json(['error' => 'El estado seleccionado no existe'], 404);
        }

        try {
            DB::beginTransaction();
        
            // Actualizar estado de la cotización
            $cotizacion->update(['estado_id' => $estadoNuevoId]);
        
            // Registrar en historial
            HistorialCotizacion::create([
                'cotizacion_id' => $cotizacion->id,
                'estado_anterior_id' => $estadoAnteriorId,
                'estado_nuevo_id' => $estadoNuevo->id,
                'user_id' => Auth::id(),
                'comentario' => $request->comentario,
            ]);
        
            // Lógica específica para estado Emitida
            $estadoEmitida = EstadoCotizacion::where('nombre', 'Emitida')->first();
            if ($estadoEmitida && $estadoNuevoId == $estadoEmitida->id && !$cotizacion->fecha_validez) {
                $cotizacion->update(['fecha_validez' => now()->addDays(30)]);
            }
        
            // Registrar seguimiento automático con campos adicionales
            $estadoAnterior = EstadoCotizacion::find($estadoAnteriorId);
            $contenido = "Cambio de estado: {$estadoAnterior->nombre} → {$estadoNuevo->nombre}\n";
            $contenido .= "Comentario: {$request->comentario}";
        
            $datosAdicionales = [];
            switch ($estadoNuevo->nombre) {
                case 'Interesado':
                    $datosAdicionales = [
                        'cotizacion_enviada' => $request->cotizacion_enviada,
                        'metodo_pago' => $request->metodo_pago,
                        'solicitud_credito' => $request->solicitud_credito,
                        'estado_credito' => $request->estado_credito,
                    ];
                    break;
                case 'NO CUMPLE PERFIL':
                    $datosAdicionales = ['motivo_rechazo' => $request->motivo_rechazo];
                    break;
                case 'CERRADO GANADO':
                    $datosAdicionales = [
                        'fecha_cierre' => $request->fecha_cierre,
                        'monto_venta' => $request->monto_venta,
                    ];
                    break;
                case 'Aceptada':
                    $datosAdicionales = ['fecha_aceptacion' => $request->fecha_aceptacion];
                    break;
                case 'Rechazada':
                    $datosAdicionales = ['motivo_rechazo_estado' => $request->motivo_rechazo_estado];
                    break;
            }
        
            SeguimientoCotizacion::create([
                'cotizacion_id' => $cotizacionId,
                'user_id' => Auth::id(),
                'tipo' => 'nota',
                'contenido' => $contenido,
                'fecha_seguimiento' => now(),
                'datos_adicionales' => array_filter($datosAdicionales, fn($value) => !is_null($value)),
            ]);
        
            DB::commit();
        
            return response()->json([
                'success' => true,
                'estado_nombre' => $estadoNuevo->nombre,
                'estado_color' => $estadoNuevo->color,
                'message' => 'Estado actualizado correctamente',
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Error al cambiar estado de cotización: " . $e->getMessage());
            return response()->json(['error' => 'Error al cambiar el estado'], 500);
        }
    }

    /**
     * Mostrar la vista de estados
     */
    public function show($cotizacionId)
    {
        $cotizacion = Cotizacion::with(['estado', 'usuario', 'ultimoSeguimiento'])->findOrFail($cotizacionId);
        return view('admin.ventas.cotizaciones.estados', compact('cotizacion'));
    }
}