<?php

namespace App\Http\Controllers\Admin\Mantenimiento;

use App\Http\Controllers\Controller;
use App\Models\SeguimientoOrdenTrabajo;
use App\Models\ComentarioSeguimientoOrden;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class ComentarioSeguimientoOrdenController extends Controller
{
    /**
     * Almacenar un nuevo comentario para el seguimiento
     */
    public function store(Request $request, SeguimientoOrdenTrabajo $seguimiento)
    {
        try {
            $validator = Validator::make($request->all(), [
                'contenido' => 'required|string',
                'archivo' => 'nullable|file|max:10240', // 10MB máximo
            ]);
    
            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error de validación',
                    'errors' => $validator->errors()
                ], 422);
            }
    
            $comentario = new ComentarioSeguimientoOrden();
            $comentario->seguimiento_id = $seguimiento->id;
            $comentario->user_id = Auth::id();
            $comentario->contenido = $request->contenido;
            
            // Manejo de archivos
            if ($request->hasFile('archivo')) {
                $file = $request->file('archivo');
                $path = $file->store('public/comentarios');
                $comentario->archivo = str_replace('public/', '', $path);
                $comentario->nombre_archivo = $file->getClientOriginalName();
                
                // Determinar si es imagen
                $extension = strtolower($file->getClientOriginalExtension());
                $comentario->es_imagen = in_array($extension, ['jpg', 'jpeg', 'png', 'gif', 'svg', 'webp']);
                $comentario->extension_archivo = $extension;
            }
            
            $comentario->save();
    
            // Cargar relaciones para devolver datos completos
            $comentario->load('usuario');
    
            return response()->json([
                'success' => true,
                'message' => 'Comentario agregado correctamente',
                'comentario' => $comentario,
                'ruta_archivo' => $comentario->archivo ? Storage::url($comentario->archivo) : null
            ]);
        } catch (\Exception $e) {
            \Log::error('Error al guardar comentario: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Error al guardar el comentario: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Actualizar un comentario existente
     */
    public function update(Request $request, SeguimientoOrdenTrabajo $seguimiento, ComentarioSeguimientoOrden $comentario)
    {
        try {
            // Verificar que el usuario actual sea el creador o tenga permisos
            if ($comentario->user_id != Auth::id() && !Auth::user()->can('editar_comentarios')) {
                return response()->json([
                    'success' => false,
                    'message' => 'No tiene permisos para editar este comentario'
                ], 403);
            }

            // Validar la solicitud
            $validator = Validator::make($request->all(), [
                'contenido' => 'required|string',
                'archivo' => 'nullable|file|max:10240', // Máximo 10MB
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error de validación',
                    'errors' => $validator->errors()
                ], 422);
            }

            // Actualizar el contenido
            $comentario->contenido = $request->contenido;

            // Procesar archivo si se proporciona uno nuevo
            if ($request->hasFile('archivo') && $request->file('archivo')->isValid()) {
                // Eliminar archivo anterior si existe
                if ($comentario->archivo && Storage::disk('public')->exists($comentario->archivo)) {
                    Storage::disk('public')->delete($comentario->archivo);
                }

                $file = $request->file('archivo');
                $nombreOriginal = $file->getClientOriginalName();
                $nombreArchivo = time() . '_' . $nombreOriginal;
                
                // Guardar el nuevo archivo
                $path = $file->storeAs('comentarios', $nombreArchivo, 'public');
                $comentario->archivo = $path;
            }

            $comentario->save();

            // Cargar relación usuario para enviarlo en la respuesta
            $comentario->load('usuario');

            return response()->json([
                'success' => true,
                'message' => 'Comentario actualizado correctamente',
                'comentario' => $comentario
            ]);
        } catch (\Exception $e) {
            Log::error('Error al actualizar comentario: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Error al actualizar el comentario: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Eliminar un comentario
     */
    public function destroy(Request $request, SeguimientoOrdenTrabajo $seguimiento, $comentarioId)
    {
        try {
            $comentario = ComentarioSeguimientoOrden::findOrFail($comentarioId);
            
            // Verificar que pertenece al seguimiento
            if ($comentario->seguimiento_id != $seguimiento->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'El comentario no pertenece a este seguimiento'
                ], 403);
            }
            
            // Verificar permisos (solo el autor o admin puede eliminar)
            if ($comentario->user_id != Auth::id() && !Auth::user()->can('eliminar_comentarios')) {
                return response()->json([
                    'success' => false,
                    'message' => 'No tiene permisos para eliminar este comentario'
                ], 403);
            }
            
            // Eliminar archivo si existe
            if ($comentario->archivo) {
                Storage::delete('public/' . $comentario->archivo);
            }
            
            $comentario->delete();
    
            return response()->json([
                'success' => true,
                'message' => 'Comentario eliminado correctamente'
            ]);
        } catch (\Exception $e) {
            \Log::error('Error al eliminar comentario: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Error al eliminar el comentario: ' . $e->getMessage()
            ], 500);
        }
    }
}