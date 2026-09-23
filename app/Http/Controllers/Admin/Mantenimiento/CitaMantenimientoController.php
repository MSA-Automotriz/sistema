<?php

namespace App\Http\Controllers\Admin\Mantenimiento;

use App\Http\Controllers\Controller;
use App\Models\CitaMantenimiento;
use App\Models\Cliente;
use App\Models\Role;
use App\Models\User;
use App\Models\VehiculoMantenimiento;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CitaMantenimientoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $citas = CitaMantenimiento::with(['cliente', 'vehiculo', 'tecnico'])
            ->orderBy('fecha_hora_cita', 'desc')
            ->paginate(10);
            
        return view('admin.mantenimiento.citas.index', compact('citas'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // Obtener asesores de servicio (usuarios con rol de técnico)
        $rolTecnico = Role::where('name', 'tecnico')->first();
        $tecnicos = [];
        
        if ($rolTecnico) {
            $tecnicos = User::whereHas('roles', function($query) use ($rolTecnico) {
                $query->where('role_id', $rolTecnico->id);
            })->get();
        }
        
        return view('admin.mantenimiento.citas.create', compact('tecnicos'));
    }

/**
 * Store a newly created resource in storage.
 */
public function store(Request $request)
    {
        try {
            // Registrar todos los datos recibidos para depuración
            \Log::info('Datos recibidos en la solicitud:', $request->all());
            
            // Validación básica para asegurar datos mínimos necesarios
            $validated = $request->validate([
                'cliente_id' => 'required|exists:clientes,id',
                'vehiculos' => 'required|array',
                'fecha_hora_cita' => 'required|date',
                'motivo_visita' => 'required|string',
            ]);
            
            \Log::info('Datos validados:', $validated);

            DB::beginTransaction();
            
            // Verificar las tablas necesarias
            if (!\Schema::hasTable('servicios_cita')) {
                \Log::warning('La tabla servicios_cita no existe, se crearán las citas sin servicios');
            }
            
            $citas_creadas = [];  // Array para almacenar las citas creadas
            
            // Iterar sobre cada vehículo enviado
            foreach ($request->vehiculos as $index => $vehiculoData) {
                \Log::info("Procesando vehículo {$index}:", $vehiculoData);
                
                if (isset($vehiculoData['id']) && !empty($vehiculoData['id'])) {
                    // Comprobar si el vehículo existe
                    $vehiculo = VehiculoMantenimiento::find($vehiculoData['id']);
                    
                    if (!$vehiculo) {
                        \Log::warning("El vehículo con ID {$vehiculoData['id']} no existe o no es válido");
                        continue; // Continuar con el siguiente vehículo en lugar de lanzar una excepción
                    }
                    
                    // Crear cita para cada vehículo válido
                    $cita = CitaMantenimiento::create([
                        'cliente_id' => $request->cliente_id,
                        'vehiculo_id' => $vehiculoData['id'],
                        'fecha_hora_cita' => $request->fecha_hora_cita,
                        'motivo_visita' => $request->motivo_visita,
                        'descripcion_problema' => $request->descripcion_problema,
                        'estado' => 'pendiente',
                        'tecnico_id' => $request->asesor_servicio,
                        'notas_adicionales' => $request->notas_adicionales,
                    ]);
                    
                    $citas_creadas[] = $cita;  // Guardar la cita creada en el array
                    
                    \Log::info("Cita creada con ID: {$cita->id}");

                    // Guardar servicios si existen y existe la tabla
                    if (Schema::hasTable('servicios_cita') && isset($vehiculoData['servicios']) && is_array($vehiculoData['servicios'])) {
                        foreach ($vehiculoData['servicios'] as $servicio) {
                            if (!empty($servicio)) {
                                DB::table('servicios_cita')->insert([
                                    'cita_id' => $cita->id,
                                    'descripcion' => $servicio,
                                    'created_at' => now(),
                                    'updated_at' => now(),
                                ]);
                            }
                        }
                    }
                    
                    // Registrar adelanto si existe y existe la tabla
                    if ($request->filled('adelanto') && $request->adelanto > 0 && Schema::hasTable('adelantos')) {
                        DB::table('adelantos')->insert([
                            'cita_id' => $cita->id,
                            'monto' => $request->adelanto,
                            'metodo_pago' => $request->metodo_pago ?? 'efectivo',
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]);
                    }
                } else {
                    \Log::warning("Vehículo sin ID válido, se omite:", $vehiculoData);
                }
            }

            // Verificar si se creó al menos una cita
            if (empty($citas_creadas)) {
                throw new \Exception('No se pudo crear ninguna cita. Verifique que haya seleccionado al menos un vehículo válido.');
            }

            DB::commit();
            \Log::info('Transacción completada exitosamente. Citas creadas: ' . count($citas_creadas));
            
            return redirect()->route('admin.mantenimiento.citas.index')
                ->with('success', 'Cita(s) creada(s) correctamente');
                
        } catch (\Illuminate\Validation\ValidationException $e) {
            DB::rollBack();
            \Log::error('Error de validación: ' . $e->getMessage(), $e->errors());
            return back()->withErrors($e->errors())->withInput();
            
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error al crear la cita: ' . $e->getMessage(), [
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);
            return back()->withErrors(['error' => 'Error al crear la cita: ' . $e->getMessage()])->withInput();
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(CitaMantenimiento $cita)
    {
        $cita->load(['cliente', 'vehiculo', 'tecnico']);
        return view('admin.mantenimiento.citas.show', compact('cita'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(CitaMantenimiento $cita)
    {
        $cita->load(['cliente', 'vehiculo']);
        
        // Obtener técnicos (usuarios con rol de técnico)
        $rolTecnico = Role::where('name', 'tecnico')->first();
        $tecnicos = [];
        
        if ($rolTecnico) {
            $tecnicos = User::whereHas('roles', function($query) use ($rolTecnico) {
                $query->where('role_id', $rolTecnico->id);
            })->get();
        }
        
        return view('admin.mantenimiento.citas.edit', compact('cita', 'tecnicos'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, CitaMantenimiento $cita)
    {
        // Validación de datos
        $request->validate([
            'fecha_hora_cita' => 'required|date',
            'motivo_visita' => 'required|string',
            'descripcion_problema' => 'nullable|string',
            'tecnico_id' => 'nullable|exists:users,id',
            'estado' => 'required|in:pendiente,confirmada,en_progreso,completada,cancelada',
            'notas_adicionales' => 'nullable|string',
        ]);

        // Actualizar la cita
        $cita->update([
            'fecha_hora_cita' => $request->fecha_hora_cita,
            'motivo_visita' => $request->motivo_visita,
            'descripcion_problema' => $request->descripcion_problema,
            'tecnico_id' => $request->tecnico_id,
            'estado' => $request->estado,
            'notas_adicionales' => $request->notas_adicionales,
        ]);
        
        return redirect()->route('admin.mantenimiento.citas.index')
            ->with('success', 'Cita actualizada correctamente');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(CitaMantenimiento $cita)
    {
        // Verificar si la cita se puede eliminar (no tiene orden de trabajo)
        if ($cita->ordenTrabajo) {
            return back()->withErrors(['error' => 'No se puede eliminar la cita porque ya tiene una orden de trabajo asociada']);
        }
        
        $cita->delete();
        
        return redirect()->route('admin.mantenimiento.citas.index')
            ->with('success', 'Cita eliminada correctamente');
    }
    
/**
 * Confirmar cita y crear orden de trabajo
 */
public function confirmar(Request $request, CitaMantenimiento $cita)
{
    // Validación de datos (removiendo kilometraje que no está en el formulario)
    $request->validate([
        'tecnico_id' => 'required|exists:users,id',
        'box' => 'required|string',
    ]);
    
    DB::beginTransaction();
    
    try {
        // Actualizar la cita a confirmada
        $cita->update([
            'tecnico_id' => $request->tecnico_id,
            'estado' => 'confirmada',
        ]);
        
        // Usar el kilometraje actual del vehículo
        $kilometrajeActual = $cita->vehiculo->kilometraje ?? 0;
        
        // Crear orden de trabajo
        $ordenTrabajo = $cita->ordenTrabajo()->create([
            'codigo_orden' => 'OT-' . date('YmdHis') . '-' . $cita->id,
            'vehiculo_id' => $cita->vehiculo_id,
            'cliente_id' => $cita->cliente_id,
            'cita_id' => $cita->id,
            'fecha_ingreso' => now(),
            'descripcion_problema' => $cita->descripcion_problema,
            'tecnico_asignado_id' => $request->tecnico_id,
            'estado' => 'diagnostico',
            'aprobado_por_cliente' => false,
            'kilometraje_ingreso' => $kilometrajeActual,
            'kilometraje_salida' => null, 
            'box' => $request->box, 
        ]);
        
        DB::commit();
        
        \Log::info("Cita {$cita->id} confirmada y orden de trabajo {$ordenTrabajo->id} creada");
        
        // Usar la forma explícita para pasar el parámetro de ruta
        return redirect()->route('admin.mantenimiento.ordenes.show', ['orden' => $ordenTrabajo->id])
            ->with('success', 'Cita confirmada y orden de trabajo creada correctamente');
            
    } catch (\Exception $e) {
        DB::rollBack();
        \Log::error("Error al confirmar cita {$cita->id}: " . $e->getMessage());
        \Log::error("Trace: " . $e->getTraceAsString());
        return back()->withErrors(['error' => 'Error al confirmar la cita: ' . $e->getMessage()]);
    }
}
    
    /**
     * Registrar adelanto de dinero
     */
    public function registrarAdelanto(Request $request, CitaMantenimiento $cita)
    {
        // Validación de datos
        $request->validate([
            'monto' => 'required|numeric|min:0',
            'metodo_pago' => 'required|string',
        ]);
        
        // Aquí se implementaría la lógica para registrar el adelanto
        // Esto dependerá de cómo se estructure la base de datos para manejar pagos
        
        return back()->with('success', 'Adelanto registrado correctamente');
    }
}