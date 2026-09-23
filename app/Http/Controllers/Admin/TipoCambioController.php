<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TipoCambio;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

class TipoCambioController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $tiposCambio = TipoCambio::with('usuario')
                                ->orderBy('fecha', 'desc')
                                ->paginate(15);
                                
        $tipoCambioActual = TipoCambio::obtenerActual();

        return view('admin.configuracion.tipos-cambio.index', compact('tiposCambio', 'tipoCambioActual'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.configuracion.tipos-cambio.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'fecha' => 'required|date',
            'compra' => 'required|numeric|min:0|max:99.9999',
            'venta' => 'required|numeric|min:0|max:99.9999',
            'fecha_inicio' => 'required|date',
            'fecha_fin' => 'nullable|date|after_or_equal:fecha_inicio',
            'origen' => 'required|in:sunat,manual',
            'activo' => 'boolean',
            'observaciones' => 'nullable|string|max:500',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                           ->withErrors($validator)
                           ->withInput();
        }

        try {
            // Si se marca como activo, desactivar otros tipos de cambio de la misma fecha
            if ($request->activo) {
                TipoCambio::where('fecha', $request->fecha)->update(['activo' => false]);
            }

            TipoCambio::create([
                'fecha' => $request->fecha,
                'compra' => $request->compra,
                'venta' => $request->venta,
                'fecha_inicio' => $request->fecha_inicio,
                'fecha_fin' => $request->fecha_fin,
                'origen' => $request->origen,
                'activo' => $request->boolean('activo'),
                'observaciones' => $request->observaciones,
                'user_id' => Auth::id(),
            ]);

            return redirect()->route('admin.configuracion.tipos-cambio.index')
                           ->with('success', 'Tipo de cambio registrado exitosamente.');

        } catch (\Exception $e) {
            Log::error('Error al crear tipo de cambio: ' . $e->getMessage());
            return redirect()->back()
                           ->with('error', 'Error al registrar el tipo de cambio.')
                           ->withInput();
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(TipoCambio $tipoCambio)
    {
        $tipoCambio->load('usuario');
        return view('admin.configuracion.tipos-cambio.show', compact('tipoCambio'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(TipoCambio $tipoCambio)
    {
        return view('admin.configuracion.tipos-cambio.edit', compact('tipoCambio'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, TipoCambio $tipoCambio)
    {
        $validator = Validator::make($request->all(), [
            'fecha' => 'required|date',
            'compra' => 'required|numeric|min:0|max:99.9999',
            'venta' => 'required|numeric|min:0|max:99.9999',
            'fecha_inicio' => 'required|date',
            'fecha_fin' => 'nullable|date|after_or_equal:fecha_inicio',
            'origen' => 'required|in:sunat,manual',
            'activo' => 'boolean',
            'observaciones' => 'nullable|string|max:500',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                           ->withErrors($validator)
                           ->withInput();
        }

        try {
            // Si se marca como activo, desactivar otros tipos de cambio de la misma fecha
            if ($request->activo && !$tipoCambio->activo) {
                TipoCambio::where('fecha', $request->fecha)
                         ->where('id', '!=', $tipoCambio->id)
                         ->update(['activo' => false]);
            }

            $tipoCambio->update([
                'fecha' => $request->fecha,
                'compra' => $request->compra,
                'venta' => $request->venta,
                'fecha_inicio' => $request->fecha_inicio,
                'fecha_fin' => $request->fecha_fin,
                'origen' => $request->origen,
                'activo' => $request->boolean('activo'),
                'observaciones' => $request->observaciones,
                'user_id' => Auth::id(),
            ]);

            return redirect()->route('admin.configuracion.tipos-cambio.index')
                           ->with('success', 'Tipo de cambio actualizado exitosamente.');

        } catch (\Exception $e) {
            Log::error('Error al actualizar tipo de cambio: ' . $e->getMessage());
            return redirect()->back()
                           ->with('error', 'Error al actualizar el tipo de cambio.')
                           ->withInput();
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(TipoCambio $tipoCambio)
    {
        try {
            $tipoCambio->delete();
            return redirect()->route('admin.configuracion.tipos-cambio.index')
                           ->with('success', 'Tipo de cambio eliminado exitosamente.');
                           
        } catch (\Exception $e) {
            Log::error('Error al eliminar tipo de cambio: ' . $e->getMessage());
            return redirect()->back()
                           ->with('error', 'Error al eliminar el tipo de cambio.');
        }
    }

    /**
     * Obtener tipo de cambio desde SUNAT API
     */
    public function obtenerDeSunat(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'fecha' => 'required|date|before_or_equal:today',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Fecha inválida.'
            ]);
        }

        try {
            $fecha = Carbon::parse($request->fecha);
            $fechaFormateada = $fecha->format('d/m/Y');

            // API de SUNAT para obtener tipo de cambio
            $response = Http::timeout(10)->get("https://api.sunat.gob.pe/v1/contribuyente/contribuyentes/tipocambio", [
                'fecha' => $fechaFormateada
            ]);

            if ($response->successful()) {
                $data = $response->json();
                
                if (isset($data['compra']) && isset($data['venta'])) {
                    return response()->json([
                        'success' => true,
                        'data' => [
                            'fecha' => $fecha->format('Y-m-d'),
                            'compra' => $data['compra'],
                            'venta' => $data['venta'],
                            'origen' => 'sunat'
                        ]
                    ]);
                }
            }

            // API alternativa si la primera falla
            $responseAlt = Http::timeout(10)->get("https://api.apis.net.pe/v1/tipo-cambio-sunat", [
                'fecha' => $fecha->format('Y-m-d')
            ]);

            if ($responseAlt->successful()) {
                $dataAlt = $responseAlt->json();
                
                if (isset($dataAlt['compra']) && isset($dataAlt['venta'])) {
                    return response()->json([
                        'success' => true,
                        'data' => [
                            'fecha' => $fecha->format('Y-m-d'),
                            'compra' => $dataAlt['compra'],
                            'venta' => $dataAlt['venta'],
                            'origen' => 'sunat'
                        ]
                    ]);
                }
            }

            return response()->json([
                'success' => false,
                'message' => 'No se pudo obtener el tipo de cambio de SUNAT para la fecha solicitada.'
            ]);

        } catch (\Exception $e) {
            Log::error('Error al obtener tipo de cambio de SUNAT: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Error al conectar con los servicios de SUNAT.'
            ]);
        }
    }

    /**
     * Activar/desactivar tipo de cambio
     */
    public function toggleActivo(TipoCambio $tipoCambio)
    {
        try {
            if (!$tipoCambio->activo) {
                // Si se va a activar, desactivar otros de la misma fecha
                TipoCambio::where('fecha', $tipoCambio->fecha)
                         ->where('id', '!=', $tipoCambio->id)
                         ->update(['activo' => false]);
            }

            $tipoCambio->update([
                'activo' => !$tipoCambio->activo,
                'user_id' => Auth::id(),
            ]);

            $estado = $tipoCambio->activo ? 'activado' : 'desactivado';

            return response()->json([
                'success' => true,
                'message' => "Tipo de cambio {$estado} exitosamente.",
                'activo' => $tipoCambio->activo
            ]);

        } catch (\Exception $e) {
            Log::error('Error al cambiar estado de tipo de cambio: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Error al cambiar el estado del tipo de cambio.'
            ]);
        }
    }

    /**
     * API para obtener tipo de cambio actual (para POS y otros módulos)
     */
    public function api()
    {
        try {
            $tipoCambio = TipoCambio::obtenerActual();
            
            if (!$tipoCambio) {
                return response()->json([
                    'success' => false,
                    'message' => 'No hay tipo de cambio disponible.'
                ]);
            }

            return response()->json([
                'success' => true,
                'data' => [
                    'fecha' => $tipoCambio->fecha->format('Y-m-d'),
                    'compra' => $tipoCambio->compra,
                    'venta' => $tipoCambio->venta,
                    'fecha_inicio' => $tipoCambio->fecha_inicio->format('Y-m-d'),
                    'fecha_fin' => $tipoCambio->fecha_fin?->format('Y-m-d'),
                    'origen' => $tipoCambio->origen_texto,
                    'vigente' => $tipoCambio->es_vigente
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Error al obtener tipo de cambio actual: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Error interno del servidor.'
            ]);
        }
    }
}
