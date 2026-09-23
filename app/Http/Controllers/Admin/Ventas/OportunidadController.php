<?php

namespace App\Http\Controllers\Admin\Ventas;

use App\Http\Controllers\Controller;
use App\Models\Oportunidad;
use App\Models\SeguimientoOportunidad;
use App\Models\Cliente;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class OportunidadController extends Controller
{
    public function index(Request $request)
    {
        $oportunidades = Oportunidad::with(['cliente', 'usuario'])
            ->when($request->estado, function ($query, $estado) {
                return $query->where('estado', $estado);
            })
            ->when($request->cliente_id, function ($query, $cliente_id) {
                return $query->where('cliente_id', $cliente_id);
            })
            ->when($request->fecha_desde && $request->fecha_hasta, function ($query) use ($request) {
                return $query->whereBetween('created_at', [$request->fecha_desde, $request->fecha_hasta]);
            })
            ->latest()
            ->get();

        return response()->json($oportunidades);
    }

    public function store(Request $request)
    {
        $request->validate([
            'titulo' => 'required|string|max:255',
            'cliente_id' => 'required|exists:clientes,id',
            'probabilidad' => 'required|integer|min:0|max:100',
            'valor_estimado' => 'nullable|numeric|min:0',
            'moneda' => 'required|in:Soles,Dólares',
            'descripcion' => 'nullable|string',
            'fecha_cierre_estimada' => 'nullable|date|after:today',
        ]);

        try {
            DB::beginTransaction();

            $oportunidad = Oportunidad::create([
                'titulo' => $request->titulo,
                'cliente_id' => $request->cliente_id,
                'probabilidad' => $request->probabilidad,
                'valor_estimado' => $request->valor_estimado,
                'moneda' => $request->moneda,
                'descripcion' => $request->descripcion,
                'estado' => 'Activa',
                'user_id' => Auth::id(),
                'fecha_cierre_estimada' => $request->fecha_cierre_estimada,
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'oportunidad' => $oportunidad->load(['cliente', 'usuario']),
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Error al crear oportunidad: " . $e->getMessage());
            return response()->json(['error' => 'Error al crear la oportunidad'], 500);
        }
    }

    public function show(Oportunidad $oportunidad)
    {
        $oportunidad->load(['cliente', 'usuario', 'seguimientos']);
        return response()->json($oportunidad);
    }

    public function update(Request $request, Oportunidad $oportunidad)
    {
        $request->validate([
            'titulo' => 'required|string|max:255',
            'cliente_id' => 'required|exists:clientes,id',
            'probabilidad' => 'required|integer|min:0|max:100',
            'valor_estimado' => 'nullable|numeric|min:0',
            'moneda' => 'required|in:Soles,Dólares',
            'descripcion' => 'nullable|string',
            'fecha_cierre_estimada' => 'nullable|date|after:today',
        ]);

        try {
            DB::beginTransaction();

            $oportunidad->update([
                'titulo' => $request->titulo,
                'cliente_id' => $request->cliente_id,
                'probabilidad' => $request->probabilidad,
                'valor_estimado' => $request->valor_estimado,
                'moneda' => $request->moneda,
                'descripcion' => $request->descripcion,
                'fecha_cierre_estimada' => $request->fecha_cierre_estimada,
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'oportunidad' => $oportunidad->load(['cliente', 'usuario']),
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Error al actualizar oportunidad: " . $e->getMessage());
            return response()->json(['error' => 'Error al actualizar la oportunidad'], 500);
        }
    }

    public function destroy(Oportunidad $oportunidad)
    {
        try {
            DB::beginTransaction();
            $oportunidad->delete();
            DB::commit();
            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Error al eliminar oportunidad: " . $e->getMessage());
            return response()->json(['error' => 'Error al eliminar la oportunidad'], 500);
        }
    }

    public function agregarSeguimiento(Request $request, Oportunidad $oportunidad)
    {
        $request->validate([
            'tipo' => 'required|in:nota,llamada,reunion,email,otro',
            'contenido' => 'required|string',
            'fecha_seguimiento' => 'required|date',
            'recordatorio' => 'boolean',
            'fecha_recordatorio' => 'nullable|required_if:recordatorio,1|date|after:fecha_seguimiento',
        ]);

        try {
            $seguimiento = SeguimientoOportunidad::create([
                'oportunidad_id' => $oportunidad->id,
                'user_id' => Auth::id(),
                'tipo' => $request->tipo,
                'contenido' => $request->contenido,
                'fecha_seguimiento' => $request->fecha_seguimiento,
                'recordatorio' => $request->recordatorio ?? false,
                'fecha_recordatorio' => $request->recordatorio ? $request->fecha_recordatorio : null,
            ]);

            return response()->json([
                'success' => true,
                'seguimiento' => $seguimiento->load('usuario'),
            ]);
        } catch (\Exception $e) {
            Log::error("Error al agregar seguimiento: " . $e->getMessage());
            return response()->json(['error' => 'Error al agregar el seguimiento'], 500);
        }
    }
}