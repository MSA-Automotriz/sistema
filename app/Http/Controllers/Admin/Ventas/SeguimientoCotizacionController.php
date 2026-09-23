<?php

namespace App\Http\Controllers\Admin\Ventas;

use App\Http\Controllers\Controller;
use App\Models\SeguimientoCotizacion;
use App\Models\Cotizacion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class SeguimientoCotizacionController extends Controller
{
    /**
     * Muestra todos los seguimientos de una cotización
     */
    public function index($cotizacionId)
    {
        $cotizacion = Cotizacion::findOrFail($cotizacionId);
        $seguimientos = $cotizacion->seguimientos()
                                 ->with('usuario')
                                 ->latest('fecha_seguimiento')
                                 ->get();

        return view('admin.ventas.cotizaciones.seguimientos.index', 
               compact('cotizacion', 'seguimientos'));
    }

    /**
     * Muestra el formulario para crear un nuevo seguimiento
     */
    public function create($cotizacionId)
    {
        $cotizacion = Cotizacion::with('estado', 'usuario')->findOrFail($cotizacionId);
        return view('admin.ventas.cotizaciones.proceso.gestion-form', compact('cotizacion'));
    }

    /**
     * Almacena un nuevo seguimiento (para uso con formularios tradicionales)
     */
    public function store(Request $request, $cotizacionId)
    {
        $request->validate([
            'tipo' => 'required|in:nota,llamada,reunion,email,otro',
            'contenido' => 'required|string',
            'fecha_seguimiento_date' => 'required|date',
            'fecha_seguimiento_time' => 'required',
            'recordatorio' => 'nullable|in:1',
            'fecha_recordatorio' => 'nullable|date',
            'hora_recordatorio' => 'nullable',
        ]);

        try {
            DB::beginTransaction();

            $cotizacion = Cotizacion::findOrFail($cotizacionId);

            SeguimientoCotizacion::create([
                'cotizacion_id' => $cotizacionId,
                'user_id' => Auth::id(),
                'tipo' => $request->tipo,
                'contenido' => $request->contenido,
                'fecha_seguimiento' => $request->fecha_seguimiento_date . ' ' . $request->fecha_seguimiento_time,
                'recordatorio' => $request->recordatorio == '1',
                'fecha_recordatorio' => $request->recordatorio == '1' ? ($request->fecha_recordatorio . ' ' . $request->hora_recordatorio) : null,
                'datos_adicionales' => [], // No se usan campos adicionales
            ]);

            DB::commit();

            return redirect()->route('admin.ventas.cotizaciones.show', $cotizacionId)
                ->with('success', 'Seguimiento registrado correctamente');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Error al registrar seguimiento: " . $e->getMessage());
            return redirect()->back()->with('error', 'Error al registrar el seguimiento');
        }
    }

    /**
     * Almacena un nuevo seguimiento (para uso en la vista de gestión)
     * Compatible con tu ruta actual seguimiento.agregar
     */
    public function agregar(Request $request, $cotizacionId)
    {
        return $this->guardarSeguimiento($request, $cotizacionId, false);
    }

    /**
     * Función centralizada para guardar seguimientos
     */
    protected function guardarSeguimiento(Request $request, $cotizacionId, $redirectToIndex)
    {
        $request->validate([
            'tipo' => 'required|in:nota,llamada,reunion,email,otro',
            'contenido' => 'required|string',
            'fecha_seguimiento' => 'required|date_format:Y-m-d\TH:i',
            'recordatorio' => 'nullable|string|in:on',
            'fecha_recordatorio' => 'nullable|date_format:Y-m-d\TH:i',
        ], [
            'contenido.required' => 'El campo comentarios es obligatorio.',
            'contenido.string' => 'El campo comentarios debe ser un texto válido.',
            'fecha_seguimiento.required' => 'La fecha de seguimiento es obligatoria.',
            'fecha_seguimiento.date_format' => 'El formato de fecha de seguimiento es inválido.',
        ]);
    
        // Procesar fecha de seguimiento desde datetime-local format
        $fecha_seguimiento = str_replace('T', ' ', $request->fecha_seguimiento) . ':00';
    
        // Procesar fecha de recordatorio
        $fecha_recordatorio = null;
        if ($request->has('recordatorio') && $request->recordatorio === 'on' && $request->filled('fecha_recordatorio')) {
            $fecha_recordatorio = str_replace('T', ' ', $request->fecha_recordatorio) . ':00';
        }
    
        $seguimiento = SeguimientoCotizacion::create([
            'cotizacion_id' => $cotizacionId,
            'user_id' => Auth::id(),
            'tipo' => $request->tipo,
            'contenido' => $request->contenido,
            'fecha_seguimiento' => $fecha_seguimiento,
            'recordatorio' => $request->has('recordatorio') && $request->recordatorio === 'on' ? true : false,
            'fecha_recordatorio' => $fecha_recordatorio,
        ]);
    
        if ($redirectToIndex) {
            return redirect()->route('admin.ventas.cotizaciones.seguimientos.index', $cotizacionId)
                ->with('success', 'Seguimiento agregado correctamente');
        }
    
        return redirect()->back()
            ->with('success', 'Seguimiento agregado correctamente');
    }

    /**
     * Muestra un seguimiento específico
     */
    public function show($cotizacionId, $seguimientoId)
    {
        $seguimiento = SeguimientoCotizacion::where('cotizacion_id', $cotizacionId)
            ->findOrFail($seguimientoId);

        return view('admin.ventas.cotizaciones.seguimientos.show', 
               compact('seguimiento'));
    }

    /**
     * Muestra el formulario para editar un seguimiento
     */
    public function edit($cotizacionId, $seguimientoId)
    {
        $seguimiento = SeguimientoCotizacion::where('cotizacion_id', $cotizacionId)
            ->findOrFail($seguimientoId);

        return view('admin.ventas.cotizaciones.seguimientos.edit', 
               compact('seguimiento'));
    }

    /**
     * Actualiza un seguimiento existente
     */
    public function update(Request $request, $cotizacionId, $seguimientoId)
    {
        $request->validate([
            'tipo' => 'required|in:nota,llamada,reunion,email,otro',
            'contenido' => 'required|string',
            'recordatorio' => 'nullable|boolean',
            'fecha_recordatorio' => 'nullable|required_if:recordatorio,1|date',
            'hora_recordatorio' => 'nullable|required_if:recordatorio,1|string',
            'fecha_recordatorio_completa' => 'nullable|date',
        ]);

        $seguimiento = SeguimientoCotizacion::where('cotizacion_id', $cotizacionId)
            ->findOrFail($seguimientoId);

        // Procesar fecha de recordatorio
        $fecha_recordatorio = null;
        if ($request->has('recordatorio') && $request->recordatorio) {
            if ($request->filled('fecha_recordatorio_completa')) {
                $fecha_recordatorio = $request->fecha_recordatorio_completa;
            } elseif ($request->filled('fecha_recordatorio') && $request->filled('hora_recordatorio')) {
                $fecha_recordatorio = $request->fecha_recordatorio . ' ' . $request->hora_recordatorio . ':00';
            }
        }

        $seguimiento->update([
            'tipo' => $request->tipo,
            'contenido' => $request->contenido,
            'recordatorio' => $request->has('recordatorio') && $request->recordatorio ? true : false,
            'fecha_recordatorio' => $fecha_recordatorio,
        ]);

        return redirect()->route('admin.ventas.cotizaciones.seguimientos.index', $cotizacionId)
            ->with('success', 'Seguimiento actualizado correctamente');
    }

    /**
     * Elimina un seguimiento
     */
    public function destroy($cotizacionId, $seguimientoId)
    {
        $seguimiento = SeguimientoCotizacion::where('cotizacion_id', $cotizacionId)
            ->findOrFail($seguimientoId);

        $seguimiento->delete();

        return redirect()->route('admin.ventas.cotizaciones.seguimientos.index', $cotizacionId)
            ->with('success', 'Seguimiento eliminado correctamente');
    }

    /**
     * Obtiene los seguimientos en formato JSON (para AJAX)
     */
    public function getSeguimientos($cotizacionId)
    {
        $cotizacion = Cotizacion::findOrFail($cotizacionId);
        $seguimientos = $cotizacion->seguimientos()
                                 ->with('usuario')
                                 ->orderBy('fecha_seguimiento', 'desc')
                                 ->get();

        return response()->json([
            'seguimientos' => $seguimientos
        ]);
    }
    
    /**
     * Muestra los recordatorios pendientes para el usuario actual
     */
    public function recordatorios()
    {
        $recordatorios = SeguimientoCotizacion::where('recordatorio', true)
            ->where(function($query) {
                $query->whereNull('fecha_recordatorio')
                    ->orWhere('fecha_recordatorio', '>=', now()->subDays(3));
            })
            ->where(function($query) {
                $query->where('user_id', Auth::id())
                    ->orWhereHas('cotizacion', function($q) {
                        $q->where('user_id', Auth::id());
                    });
            })
            ->with(['cotizacion', 'cotizacion.cliente', 'usuario'])
            ->orderBy('fecha_recordatorio')
            ->get();
            
        return view('admin.ventas.cotizaciones.recordatorios', compact('recordatorios'));
    }
    
    /**
     * Marca un recordatorio como completado
     */
    public function completarRecordatorio($id)
    {
        $seguimiento = SeguimientoCotizacion::findOrFail($id);
        
        // Verificar si el usuario tiene permiso para modificar este recordatorio
        if ($seguimiento->user_id != Auth::id() && $seguimiento->cotizacion->user_id != Auth::id()) {
            return redirect()->back()->with('error', 'No tienes permiso para modificar este recordatorio');
        }
        
        $seguimiento->update([
            'recordatorio' => false
        ]);
        
        return redirect()->back()->with('success', 'Recordatorio marcado como completado');
    }
    /**
     * Marca un seguimiento como realizado o no realizado
     */
    public function toggleRealizado($id)
    {
        $seguimiento = SeguimientoCotizacion::findOrFail($id);
        
        // Verificar si el usuario tiene permiso para modificar este seguimiento
        if ($seguimiento->user_id != Auth::id() && $seguimiento->cotizacion->user_id != Auth::id()) {
            return response()->json([
                'success' => false,
                'message' => 'No tienes permiso para modificar este seguimiento'
            ], 403);
        }
        
        $seguimiento->update([
            'realizado' => !$seguimiento->realizado
        ]);
        
        return response()->json([
            'success' => true,
            'realizado' => $seguimiento->realizado,
            'message' => $seguimiento->realizado ? 'Seguimiento marcado como realizado' : 'Seguimiento marcado como no realizado'
        ]);
    }
}