<?php

namespace App\Http\Controllers\Admin\Configuracion;

use App\Http\Controllers\Controller;
use App\Models\ReglaVencimientoCotizacion;
use App\Models\EstadoCotizacion;
use App\Models\User;
use Illuminate\Http\Request;

class ReglaVencimientoCotizacionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $reglas = ReglaVencimientoCotizacion::with('estadoVencido')
            ->paginate(10);
            
        return view('admin.configuracion.reglas-vencimiento-cotizaciones.index', compact('reglas'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $estados = EstadoCotizacion::all();
        $usuarios = User::all();
        
        return view('admin.configuracion.reglas-vencimiento-cotizaciones.create', compact('estados', 'usuarios'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nombre' => 'required|string|max:255|unique:reglas_vencimiento_cotizaciones',
            'descripcion' => 'nullable|string',
            'dias_vencimiento' => 'required|integer|min:1|max:365',
            'dias_alerta' => 'nullable|integer|min:0|max:365',
            'estado_vencido_id' => 'required|exists:estados_cotizacion,id',
            'permite_reasignacion' => 'boolean',
            'requiere_aprobacion' => 'boolean',
            'notificar_vencimiento' => 'boolean',
            'activo' => 'boolean',
            'usuarios_seleccionados' => 'array',
            'usuarios_seleccionados.*' => 'exists:users,id'
        ]);

        // Preparar condiciones
        $condiciones = [];
        if (!empty($validated['usuarios_seleccionados'])) {
            $condiciones['usuarios'] = $validated['usuarios_seleccionados'];
        }

        $regla = ReglaVencimientoCotizacion::create([
            'nombre' => $validated['nombre'],
            'descripcion' => $validated['descripcion'],
            'dias_vencimiento' => $validated['dias_vencimiento'],
            'dias_alerta' => $validated['dias_alerta'] ?? 0,
            'estado_vencido_id' => $validated['estado_vencido_id'],
            'permite_reasignacion' => $request->boolean('permite_reasignacion'),
            'requiere_aprobacion' => $request->boolean('requiere_aprobacion'),
            'notificar_vencimiento' => $request->boolean('notificar_vencimiento'),
            'activo' => $request->boolean('activo', true),
            'condiciones' => empty($condiciones) ? null : $condiciones
        ]);

        return redirect()
            ->route('admin.configuracion.reglas-vencimiento-cotizaciones.index')
            ->with('success', 'Regla de vencimiento creada exitosamente.');
    }

    /**
     * Display the specified resource.
     */
    public function show(ReglaVencimientoCotizacion $regla)
    {
        $regla->load('estadoVencido', 'cotizaciones');
        
        return view('admin.configuracion.reglas-vencimiento-cotizaciones.show', compact('regla'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(ReglaVencimientoCotizacion $regla)
    {
        $estados = EstadoCotizacion::all();
        $usuarios = User::all();
        
        return view('admin.configuracion.reglas-vencimiento-cotizaciones.edit', compact('regla', 'estados', 'usuarios'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, ReglaVencimientoCotizacion $regla)
    {
        $validated = $request->validate([
            'nombre' => 'required|string|max:255|unique:reglas_vencimiento_cotizaciones,nombre,' . $regla->id,
            'descripcion' => 'nullable|string',
            'dias_vencimiento' => 'required|integer|min:1|max:365',
            'dias_alerta' => 'nullable|integer|min:0|max:365',
            'estado_vencido_id' => 'required|exists:estados_cotizacion,id',
            'permite_reasignacion' => 'boolean',
            'requiere_aprobacion' => 'boolean',
            'notificar_vencimiento' => 'boolean',
            'activo' => 'boolean',
            'usuarios_seleccionados' => 'array',
            'usuarios_seleccionados.*' => 'exists:users,id'
        ]);

        // Preparar condiciones
        $condiciones = [];
        if (!empty($validated['usuarios_seleccionados'])) {
            $condiciones['usuarios'] = $validated['usuarios_seleccionados'];
        }

        $regla->update([
            'nombre' => $validated['nombre'],
            'descripcion' => $validated['descripcion'],
            'dias_vencimiento' => $validated['dias_vencimiento'],
            'dias_alerta' => $validated['dias_alerta'] ?? 0,
            'estado_vencido_id' => $validated['estado_vencido_id'],
            'permite_reasignacion' => $request->boolean('permite_reasignacion'),
            'requiere_aprobacion' => $request->boolean('requiere_aprobacion'),
            'notificar_vencimiento' => $request->boolean('notificar_vencimiento'),
            'activo' => $request->boolean('activo'),
            'condiciones' => empty($condiciones) ? null : $condiciones
        ]);

        return redirect()
            ->route('admin.configuracion.reglas-vencimiento-cotizaciones.index')
            ->with('success', 'Regla de vencimiento actualizada exitosamente.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ReglaVencimientoCotizacion $regla)
    {
        // Verificar si la regla está siendo usada
        if ($regla->cotizaciones()->count() > 0) {
            return redirect()
                ->route('admin.configuracion.reglas-vencimiento-cotizaciones.index')
                ->with('error', 'No se puede eliminar la regla porque está siendo utilizada en cotizaciones.');
        }

        $regla->delete();

        return redirect()
            ->route('admin.configuracion.reglas-vencimiento-cotizaciones.index')
            ->with('success', 'Regla de vencimiento eliminada exitosamente.');
    }

    /**
     * Toggle the activo status of the rule.
     */
    public function toggleActivo(ReglaVencimientoCotizacion $regla)
    {
        $regla->update(['activo' => !$regla->activo]);

        $mensaje = $regla->activo ? 'Regla activada exitosamente.' : 'Regla desactivada exitosamente.';

        return redirect()
            ->route('admin.configuracion.reglas-vencimiento-cotizaciones.index')
            ->with('success', $mensaje);
    }
}
