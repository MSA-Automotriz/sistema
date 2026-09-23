<?php

namespace App\Http\Controllers\Admin\Ventas;

use App\Http\Controllers\Controller;
use App\Models\ComentarioSeguimiento;
use App\Models\SeguimientoCotizacion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ComentarioSeguimientoController extends Controller
{
    /**
     * Obtiene los comentarios de un seguimiento
     */
    public function index($seguimientoId)
    {
        $seguimiento = SeguimientoCotizacion::with(['usuario', 'comentarios.usuario'])->findOrFail($seguimientoId);
        
        return response()->json([
            'seguimiento' => $seguimiento,
            'comentarios' => $seguimiento->comentarios
        ]);
    }
    
    /**
     * Almacena un nuevo comentario
     */
    public function store(Request $request, $seguimientoId)
    {
        $request->validate([
            'contenido' => 'required|string',
            'archivo' => 'nullable|file|max:10240', // 10MB máximo
        ]);
        
        $seguimiento = SeguimientoCotizacion::findOrFail($seguimientoId);
        
        $comentario = new ComentarioSeguimiento([
            'seguimiento_id' => $seguimientoId,
            'user_id' => Auth::id(),
            'contenido' => $request->contenido,
        ]);
        
        // Procesar archivo si existe
        if ($request->hasFile('archivo') && $request->file('archivo')->isValid()) {
            $archivo = $request->file('archivo');
            $nombreArchivo = time() . '_' . $archivo->getClientOriginalName();
            $ruta = $archivo->storeAs('comentarios', $nombreArchivo, 'public');
            $comentario->archivo = $ruta;
        }
        
        $comentario->save();
        
        // Cargar relación de usuario
        $comentario->load('usuario');
        
        return response()->json([
            'success' => true,
            'comentario' => $comentario,
            'message' => 'Comentario agregado correctamente'
        ]);
    }
    
    /**
     * Actualiza un comentario existente
     */
    public function update(Request $request, $seguimientoId, $comentarioId)
    {
        $request->validate([
            'contenido' => 'required|string',
            'archivo' => 'nullable|file|max:10240', // 10MB máximo
        ]);
        
        $comentario = ComentarioSeguimiento::where('seguimiento_id', $seguimientoId)
            ->where('id', $comentarioId)
            ->firstOrFail();
            
        // Verificar permisos
        if ($comentario->user_id != Auth::id()) {
            return response()->json([
                'success' => false,
                'message' => 'No tienes permiso para editar este comentario'
            ], 403);
        }
        
        $comentario->contenido = $request->contenido;
        
        // Procesar archivo si existe uno nuevo
        if ($request->hasFile('archivo') && $request->file('archivo')->isValid()) {
            // Eliminar archivo anterior si existe
            if ($comentario->archivo) {
                Storage::disk('public')->delete($comentario->archivo);
            }
            
            $archivo = $request->file('archivo');
            $nombreArchivo = time() . '_' . $archivo->getClientOriginalName();
            $ruta = $archivo->storeAs('comentarios', $nombreArchivo, 'public');
            $comentario->archivo = $ruta;
        }
        
        $comentario->save();
        
        // Cargar relación de usuario
        $comentario->load('usuario');
        
        return response()->json([
            'success' => true,
            'comentario' => $comentario,
            'message' => 'Comentario actualizado correctamente'
        ]);
    }
    
    /**
     * Elimina un comentario
     */
    public function destroy($seguimientoId, $comentarioId)
    {
        $comentario = ComentarioSeguimiento::where('seguimiento_id', $seguimientoId)
            ->where('id', $comentarioId)
            ->firstOrFail();
            
        // Verificar permisos
        if ($comentario->user_id != Auth::id()) {
            return response()->json([
                'success' => false,
                'message' => 'No tienes permiso para eliminar este comentario'
            ], 403);
        }
        
        // Eliminar archivo si existe
        if ($comentario->archivo) {
            Storage::disk('public')->delete($comentario->archivo);
        }
        
        $comentario->delete();
        
        return response()->json([
            'success' => true,
            'message' => 'Comentario eliminado correctamente'
        ]);
    }
}