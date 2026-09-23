<?php

namespace App\Http\Controllers\Admin\Mantenimiento;

use App\Http\Controllers\Controller;
use App\Models\OrdenTrabajoMantenimiento;
use App\Models\SeguimientoOrdenTrabajo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class SeguimientoOrdenTrabajoController extends Controller
{
    /**
     * Almacenar un nuevo seguimiento para la orden de trabajo
     */
    public function store(Request $request, OrdenTrabajoMantenimiento $orden)
    {
        try {
            // Log: Inicio del método store
            Log::info('Iniciando registro de seguimiento', [
                'orden_id' => $orden->id,
                'user_id' => Auth::id(),
                'request_data' => $request->all()
            ]);

            // Validar la solicitud
            $validator = Validator::make($request->all(), [
                'tipo' => 'required|in:nota,llamada,reunion,email',
                'contenido' => 'required|string',
                'recordatorio' => 'nullable|boolean',
                'fecha_recordatorio' => 'nullable|required_if:recordatorio,1|date',
            ]);

            if ($validator->fails()) {
                // Log: Error de validación
                Log::warning('Error de validación en registro de seguimiento', [
                    'orden_id' => $orden->id,
                    'errors' => $validator->errors()->toArray()
                ]);
                return response()->json([
                    'success' => false,
                    'message' => 'Error de validación',
                    'errors' => $validator->errors()
                ], 422);
            }

            // Log: Validación exitosa, creando seguimiento
            Log::info('Validación exitosa, creando nuevo seguimiento', [
                'orden_id' => $orden->id,
                'tipo' => $request->tipo,
                'contenido' => $request->contenido,
                'recordatorio' => $request->has('recordatorio') ? $request->recordatorio : false,
                'fecha_recordatorio' => $request->fecha_recordatorio ?? null
            ]);

            $seguimiento = new SeguimientoOrdenTrabajo();
            $seguimiento->orden_trabajo_id = $orden->id;
            $seguimiento->user_id = Auth::id();
            $seguimiento->tipo = $request->tipo;
            $seguimiento->contenido = $request->contenido;
            $seguimiento->fecha_seguimiento = now();
            $seguimiento->recordatorio = $request->has('recordatorio') ? $request->recordatorio : false;
            
            if ($seguimiento->recordatorio && $request->fecha_recordatorio) {
                $seguimiento->fecha_recordatorio = $request->fecha_recordatorio;
                // Log: Asignando fecha de recordatorio
                Log::info('Asignando fecha de recordatorio', [
                    'orden_id' => $orden->id,
                    'fecha_recordatorio' => $request->fecha_recordatorio
                ]);
            }
            
            $seguimiento->realizado = false;

            // Log: Antes de guardar el seguimiento
            Log::info('Guardando seguimiento en la base de datos', [
                'orden_id' => $orden->id,
                'seguimiento_data' => $seguimiento->toArray()
            ]);

            $seguimiento->save();

            // Log: Seguimiento guardado exitosamente
            Log::info('Seguimiento registrado correctamente', [
                'orden_id' => $orden->id,
                'seguimiento_id' => $seguimiento->id
            ]);

            // Si es solicitud AJAX
            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Seguimiento registrado correctamente',
                    'seguimiento' => $seguimiento->load('usuario')
                ]);
            }

            // Si es solicitud normal
            return redirect()->back()->with('success', 'Seguimiento registrado correctamente');
        } catch (\Exception $e) {
            // Log: Error detallado
            Log::error('Error al registrar seguimiento: ' . $e->getMessage(), [
                'orden_id' => $orden->id,
                'user_id' => Auth::id(),
                'request_data' => $request->all(),
                'trace' => $e->getTraceAsString()
            ]);
            
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error al registrar el seguimiento: ' . $e->getMessage()
                ], 500);
            }
            
            return redirect()->back()->withErrors(['error' => 'Error al registrar el seguimiento']);
        }
    }

    /**
     * Actualizar el estado de realizado de un seguimiento
     */
    public function toggleRealizado(Request $request, SeguimientoOrdenTrabajo $seguimiento)
    {
        try {
            $seguimiento->realizado = !$seguimiento->realizado;
            $seguimiento->save();
    
            return response()->json([
                'success' => true,
                'message' => $seguimiento->realizado ? 
                    'Seguimiento marcado como realizado' : 
                    'Seguimiento marcado como pendiente',
                'realizado' => $seguimiento->realizado
            ]);
        } catch (\Exception $e) {
            \Log::error('Error al actualizar estado del seguimiento: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Error al actualizar el estado del seguimiento'
            ], 500);
        }
    }

    /**
     * Eliminar un seguimiento
     */
    public function destroy(Request $request, SeguimientoOrdenTrabajo $seguimiento)
    {
        try {
            // Verificar que el usuario actual sea el creador o tenga permisos
            if ($seguimiento->user_id != Auth::id() && !Auth::user()->can('eliminar_seguimientos')) {
                if ($request->ajax()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'No tiene permisos para eliminar este seguimiento'
                    ], 403);
                }
                
                return redirect()->back()->withErrors(['error' => 'No tiene permisos para eliminar este seguimiento']);
            }

            // Eliminar comentarios relacionados
            $seguimiento->comentarios()->delete();
            
            // Eliminar el seguimiento
            $seguimiento->delete();

            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Seguimiento eliminado correctamente'
                ]);
            }

            return redirect()->back()->with('success', 'Seguimiento eliminado correctamente');
        } catch (\Exception $e) {
            Log::error('Error al eliminar seguimiento: ' . $e->getMessage());
            
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error al eliminar el seguimiento'
                ], 500);
            }
            
            return redirect()->back()->withErrors(['error' => 'Error al eliminar el seguimiento']);
        }
    }

    /**
     * Obtener los comentarios de un seguimiento
     */
    public function getComentarios(Request $request, SeguimientoOrdenTrabajo $seguimiento)
    {
        try {
            // Cargar el seguimiento con sus comentarios y usuarios relacionados
            $seguimiento->load([
                'usuario', 
                'comentarios' => function($query) {
                    $query->orderBy('created_at', 'desc');
                },
                'comentarios.usuario'
            ]);
    
            return response()->json([
                'success' => true,
                'seguimiento' => $seguimiento,
                'comentarios' => $seguimiento->comentarios
            ]);
        } catch (\Exception $e) {
            \Log::error('Error al obtener comentarios: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener los comentarios'
            ], 500);
        }
    }

    public function sidebar($seguimiento)
    {
        $seguimiento = SeguimientoOrdenTrabajo::findOrFail($seguimiento);
        return view('admin.mantenimiento.ordenes.seguimiento.sidebar', compact('seguimiento'));
    }
}