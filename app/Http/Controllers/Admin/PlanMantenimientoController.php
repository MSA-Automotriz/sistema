<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PlanMantenimiento;
use App\Models\ComponentePlanMantenimiento;
use App\Models\IntervaloPlanMantenimiento;
use App\Models\Parte;
use App\Models\Proveedor;
use App\Models\TipoCambio;
use App\Models\Modelo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class PlanMantenimientoController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = PlanMantenimiento::with(['proveedorPredeterminado', 'usuario'])
            ->orderBy('created_at', 'desc');

        // Filtros
        if ($request->filled('buscar')) {
            $buscar = $request->buscar;
            $query->where(function($q) use ($buscar) {
                $q->where('nombre', 'like', "%{$buscar}%")
                  ->orWhere('modelo_vehiculo', 'like', "%{$buscar}%")
                  ->orWhere('descripcion', 'like', "%{$buscar}%");
            });
        }

        if ($request->filled('modelo')) {
            $query->porModelo($request->modelo);
        }

        if ($request->filled('ano')) {
            $query->porAno($request->ano);
        }

        if ($request->filled('estado')) {
            if ($request->estado === 'activo') {
                $query->activo();
            } else {
                $query->where('activo', false);
            }
        }

        $planes = $query->paginate(15)->withQueryString();

        // Para los filtros
        $modelosVehiculo = PlanMantenimiento::select('modelo_vehiculo')
            ->distinct()
            ->orderBy('modelo_vehiculo')
            ->pluck('modelo_vehiculo');

        $anos = PlanMantenimiento::select('ano_modelo')
            ->distinct()
            ->orderBy('ano_modelo', 'desc')
            ->pluck('ano_modelo');

        return view('admin.planes-mantenimiento.index', compact('planes', 'modelosVehiculo', 'anos'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $partes = Parte::with(['unidad', 'categoriaParte'])
            ->orderBy('nombre')
            ->get();

        $proveedores = Proveedor::orderBy('razon_social')
            ->orderBy('nombres')
            ->get();

        $modelos = Modelo::with('marca')
            ->orderBy('nombre')
            ->get();

        $tipoCambio = TipoCambio::obtenerActual();

        return view('admin.planes-mantenimiento.create', compact('partes', 'proveedores', 'modelos', 'tipoCambio'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nombre' => 'required|string|max:255',
            'descripcion' => 'nullable|string',
            'modelo_vehiculo' => 'required|string|max:255',
            'ano_modelo' => 'required|integer|min:1990|max:' . (date('Y') + 2),
            'tipo_transmision' => 'required|in:MT,AT,CVT',
            'tono_vehiculo' => 'nullable|string|max:255',
            'intervalo_base' => 'required|integer|min:1000',
            'kilometraje_maximo' => 'required|integer|min:5000',
            'relacion_horas_km' => 'required|integer|min:50',
            'tarifa_mano_obra' => 'nullable|numeric|min:0',
            'impuestos' => 'required|numeric|min:0|max:100',
            'margen_beneficio' => 'nullable|numeric|min:0|max:100',
            'moneda_principal' => 'required|in:USD,PEN',
            'proveedor_predeterminado_id' => 'nullable|exists:proveedores,id',
            'mostrar_precios' => 'boolean',
            'activo' => 'boolean',
            
            // Componentes
            'componentes' => 'required|array|min:1',
            'componentes.*.parte_id' => 'required|exists:partes,id',
            'componentes.*.cantidad' => 'required|numeric|min:0.01',
            'componentes.*.unidad_medida' => 'required|string',
            'componentes.*.accion' => 'required|in:Reemplazar,Inspeccionar,Lubricar',
            'componentes.*.proveedor_id' => 'nullable|exists:proveedores,id',
            'componentes.*.precio_base' => 'nullable|numeric|min:0',
            'componentes.*.moneda' => 'required|in:USD,PEN',
            'componentes.*.observaciones' => 'nullable|string',
            
            // Intervalos por componente
            'intervalos' => 'array',
            'intervalos.*' => 'array',
            'intervalos.*.*.aplica' => 'boolean',
            'intervalos.*.*.cantidad_especifica' => 'nullable|numeric|min:0',
            'intervalos.*.*.precio_especifico' => 'nullable|numeric|min:0',
        ]);

        DB::beginTransaction();
        try {
            // Crear el plan de mantenimiento
            $plan = PlanMantenimiento::create([
                'nombre' => $validated['nombre'],
                'descripcion' => $validated['descripcion'],
                'modelo_vehiculo' => $validated['modelo_vehiculo'],
                'ano_modelo' => $validated['ano_modelo'],
                'tipo_transmision' => $validated['tipo_transmision'],
                'tono_vehiculo' => $validated['tono_vehiculo'],
                'intervalo_base' => $validated['intervalo_base'],
                'kilometraje_maximo' => $validated['kilometraje_maximo'],
                'relacion_horas_km' => $validated['relacion_horas_km'],
                'tarifa_mano_obra' => $validated['tarifa_mano_obra'] ?? 0,
                'impuestos' => $validated['impuestos'],
                'margen_beneficio' => $validated['margen_beneficio'] ?? 0,
                'moneda_principal' => $validated['moneda_principal'],
                'proveedor_predeterminado_id' => $validated['proveedor_predeterminado_id'],
                'mostrar_precios' => $request->boolean('mostrar_precios', true),
                'activo' => $request->boolean('activo', true),
                'user_id' => Auth::id(),
            ]);

            // Generar intervalos de kilometraje
            $intervalos = $plan->generarIntervalos();

            // Crear componentes e intervalos
            foreach ($validated['componentes'] as $index => $componenteData) {
                $componente = ComponentePlanMantenimiento::create([
                    'plan_mantenimiento_id' => $plan->id,
                    'parte_id' => $componenteData['parte_id'],
                    'cantidad' => $componenteData['cantidad'],
                    'unidad_medida' => $componenteData['unidad_medida'],
                    'accion' => $componenteData['accion'],
                    'proveedor_id' => $componenteData['proveedor_id'],
                    'precio_base' => $componenteData['precio_base'],
                    'moneda' => $componenteData['moneda'],
                    'observaciones' => $componenteData['observaciones'],
                    'activo' => true,
                ]);

                // Crear intervalos para este componente
                foreach ($intervalos as $km) {
                    $intervaloData = $validated['intervalos'][$index][$km] ?? [];
                    
                    IntervaloPlanMantenimiento::create([
                        'plan_mantenimiento_id' => $plan->id,
                        'componente_plan_id' => $componente->id,
                        'kilometraje' => $km,
                        'horas' => ($km * $plan->relacion_horas_km) / 5000,
                        'cantidad_especifica' => $intervaloData['cantidad_especifica'] ?? null,
                        'precio_especifico' => $intervaloData['precio_especifico'] ?? null,
                        'moneda_precio' => $componenteData['moneda'],
                        'aplica' => $intervaloData['aplica'] ?? false,
                        'notas' => null,
                    ]);
                }
            }

            DB::commit();

            return redirect()
                ->route('admin.planes-mantenimiento.show', $plan)
                ->with('success', 'Plan de mantenimiento creado exitosamente');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()
                ->withInput()
                ->with('error', 'Error al crear el plan de mantenimiento: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(PlanMantenimiento $planMantenimiento)
    {
        $planMantenimiento->load([
            'proveedorPredeterminado',
            'usuario',
            'componentesPlan.parte.unidad',
            'componentesPlan.parte.categoriaParte',
            'componentesPlan.proveedor',
            'componentesPlan.intervalos' => function($query) {
                $query->orderBy('kilometraje');
            }
        ]);

        $intervalos = $planMantenimiento->generarIntervalos();

        return view('admin.planes-mantenimiento.show', compact('planMantenimiento', 'intervalos'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(PlanMantenimiento $planMantenimiento)
    {
        $planMantenimiento->load([
            'componentesPlan.parte',
            'componentesPlan.proveedor',
            'componentesPlan.intervalos'
        ]);

        $partes = Parte::with(['unidad', 'categoriaParte'])
            ->orderBy('nombre')
            ->get();

        $proveedores = Proveedor::orderBy('razon_social')
            ->orderBy('nombres')
            ->get();

        $modelos = Modelo::with('marca')
            ->orderBy('nombre')
            ->get();

        $tipoCambio = TipoCambio::obtenerActual();

        return view('admin.planes-mantenimiento.edit', compact('planMantenimiento', 'partes', 'proveedores', 'modelos', 'tipoCambio'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, PlanMantenimiento $planMantenimiento)
    {
        $validated = $request->validate([
            'nombre' => 'required|string|max:255',
            'descripcion' => 'nullable|string',
            'modelo_vehiculo' => 'required|string|max:255',
            'ano_modelo' => 'required|integer|min:1990|max:' . (date('Y') + 2),
            'tipo_transmision' => 'required|in:MT,AT,CVT',
            'tono_vehiculo' => 'nullable|string|max:255',
            'intervalo_base' => 'required|integer|min:1000',
            'kilometraje_maximo' => 'required|integer|min:5000',
            'relacion_horas_km' => 'required|integer|min:50',
            'tarifa_mano_obra' => 'nullable|numeric|min:0',
            'impuestos' => 'required|numeric|min:0|max:100',
            'margen_beneficio' => 'nullable|numeric|min:0|max:100',
            'moneda_principal' => 'required|in:USD,PEN',
            'proveedor_predeterminado_id' => 'nullable|exists:proveedores,id',
            'mostrar_precios' => 'boolean',
            'activo' => 'boolean',
        ]);

        DB::beginTransaction();
        try {
            $planMantenimiento->update($validated);

            DB::commit();

            return redirect()
                ->route('admin.planes-mantenimiento.show', $planMantenimiento)
                ->with('success', 'Plan de mantenimiento actualizado exitosamente');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()
                ->withInput()
                ->with('error', 'Error al actualizar el plan de mantenimiento: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(PlanMantenimiento $planMantenimiento)
    {
        try {
            $planMantenimiento->delete();

            return redirect()
                ->route('admin.planes-mantenimiento.index')
                ->with('success', 'Plan de mantenimiento eliminado exitosamente');

        } catch (\Exception $e) {
            return back()
                ->with('error', 'Error al eliminar el plan de mantenimiento: ' . $e->getMessage());
        }
    }

    /**
     * Cambiar estado activo/inactivo
     */
    public function toggleStatus(PlanMantenimiento $planMantenimiento)
    {
        $planMantenimiento->update([
            'activo' => !$planMantenimiento->activo
        ]);

        $estado = $planMantenimiento->activo ? 'activado' : 'desactivado';
        
        return back()->with('success', "Plan de mantenimiento {$estado} exitosamente");
    }

    /**
     * Duplicar un plan de mantenimiento
     */
    public function duplicate(PlanMantenimiento $planMantenimiento)
    {
        DB::beginTransaction();
        try {
            $planOriginal = $planMantenimiento->load(['componentesPlan.intervalos']);
            
            // Crear nuevo plan
            $nuevoPlan = $planOriginal->replicate();
            $nuevoPlan->nombre = $planOriginal->nombre . ' (Copia)';
            $nuevoPlan->user_id = Auth::id();
            $nuevoPlan->save();

            // Duplicar componentes e intervalos
            foreach ($planOriginal->componentesPlan as $componente) {
                $nuevoComponente = $componente->replicate();
                $nuevoComponente->plan_mantenimiento_id = $nuevoPlan->id;
                $nuevoComponente->save();

                // Duplicar intervalos del componente
                foreach ($componente->intervalos as $intervalo) {
                    $nuevoIntervalo = $intervalo->replicate();
                    $nuevoIntervalo->plan_mantenimiento_id = $nuevoPlan->id;
                    $nuevoIntervalo->componente_plan_id = $nuevoComponente->id;
                    $nuevoIntervalo->save();
                }
            }

            DB::commit();

            return redirect()
                ->route('admin.planes-mantenimiento.edit', $nuevoPlan)
                ->with('success', 'Plan de mantenimiento duplicado exitosamente');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()
                ->with('error', 'Error al duplicar el plan de mantenimiento: ' . $e->getMessage());
        }
    }
}
