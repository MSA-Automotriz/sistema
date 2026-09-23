<?php

namespace App\Http\Controllers\Admin\Mantenimiento;

use App\Http\Controllers\Controller;
use App\Models\CitaMantenimiento;
use App\Models\DetalleOrdenTrabajoRepuesto;
use App\Models\DetalleOrdenTrabajoServicio;
use App\Models\FacturaOrdenTrabajo;
use App\Models\OrdenTrabajoMantenimiento;
use App\Models\Parte;
use App\Models\Servicio;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class OrdenTrabajoMantenimientoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $ordenes = OrdenTrabajoMantenimiento::with(['vehiculo', 'cliente', 'tecnico'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);
            
        return view('admin.mantenimiento.ordenes.index', compact('ordenes'));
    }

    /**
     * Display the specified resource.
     */
    public function show(OrdenTrabajoMantenimiento $orden)
    {
        $orden->load([
            'vehiculo',
            'cliente',
            'tecnico',
            'detallesRepuestos',
            'detallesServicios',
            'factura',
            'seguimientos.usuario',
            'seguimientos.comentarios'
        ]);
        
        // Obtener los valores del ENUM de la columna estado
        $enumStates = DB::select("SHOW COLUMNS FROM ordenes_trabajo_mantenimiento WHERE Field = 'estado'");
        $states = [];
        if (!empty($enumStates)) {
            preg_match("/^enum\((.*)\)$/", $enumStates[0]->Type, $matches);
            $states = array_map(function($value) {
                return trim($value, "'");
            }, explode(',', $matches[1]));
        }
        
        // Cargar partes disponibles para agregar
        $partes = Parte::orderBy('nombre')->get();
        
        // Cargar servicios disponibles para agregar
        $servicios = Servicio::orderBy('nombre')->get();
        
        Log::info('Cargando orden con seguimientos', [
            'orden_id' => $orden->id,
            'seguimientos_count' => $orden->seguimientos->count()
        ]);
        
        return view('admin.mantenimiento.ordenes.show', compact('orden', 'partes', 'servicios', 'states'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(OrdenTrabajoMantenimiento $orden)
    {
        $orden->load(['vehiculo', 'cliente', 'tecnico', 'detallesRepuestos', 'detallesServicios']);
        
        return view('admin.mantenimiento.ordenes.edit', compact('orden'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, OrdenTrabajoMantenimiento $orden)
    {
        // Validación de datos
        $request->validate([
            'diagnostico' => 'nullable|string',
            'recomendaciones' => 'nullable|string',
            'estado' => 'required|string',
            'fecha_proxima_revision' => 'nullable|date',
        ]);
        
        // Actualizar la orden de trabajo
        $orden->update([
            'diagnostico' => $request->diagnostico,
            'recomendaciones' => $request->recomendaciones,
            'estado' => $request->estado,
            'fecha_proxima_revision' => $request->fecha_proxima_revision,
        ]);
        
        return redirect()->route('admin.mantenimiento.ordenes.show', $orden)
            ->with('success', 'Orden de trabajo actualizada correctamente');
    }
    
    /**
     * Agregar un repuesto a la orden de trabajo
     */
    public function agregarRepuesto(Request $request, OrdenTrabajoMantenimiento $orden)
    {
        // Validación de datos
        $request->validate([
            'parte_id' => 'required|exists:partes,id',
            'cantidad' => 'required|integer|min:1',
            'precio_unitario' => 'required|numeric|min:0',
        ]);
        
        // Obtener la parte
        $parte = Parte::findOrFail($request->parte_id);
        
        // Crear el detalle de repuesto
        $detalleRepuesto = DetalleOrdenTrabajoRepuesto::create([
            'orden_trabajo_id' => $orden->id,
            'parte_id' => $parte->id,
            'cantidad' => $request->cantidad,
            'precio_unitario' => $request->precio_unitario,
            'descripcion' => $parte->nombre,
        ]);
        
        return back()->with('success', 'Repuesto agregado correctamente');
    }
    
    /**
     * Agregar un servicio a la orden de trabajo
     */
    public function agregarServicio(Request $request, OrdenTrabajoMantenimiento $orden)
    {
        // Validación de datos
        $request->validate([
            'servicio_id' => 'required|exists:servicios,id',
            'cantidad' => 'required|integer|min:1',
            'precio_unitario' => 'required|numeric|min:0',
        ]);
        
        // Obtener el servicio
        $servicio = Servicio::findOrFail($request->servicio_id);
        
        // Crear el detalle de servicio
        $detalleServicio = DetalleOrdenTrabajoServicio::create([
            'orden_trabajo_id' => $orden->id,
            'servicio_id' => $servicio->id,
            'cantidad' => $request->cantidad,
            'precio_unitario' => $request->precio_unitario,
            'descripcion' => $servicio->nombre,
        ]);
        
        return back()->with('success', 'Servicio agregado correctamente');
    }
    
    /**
     * Registrar ingreso del vehículo a sala de servicios
     */
    public function registrarIngreso(Request $request, OrdenTrabajoMantenimiento $orden)
    {
        // Validación de datos
        $request->validate([
            'kilometraje_ingreso' => 'required|integer|min:0',
        ]);
        
        // Actualizar la orden de trabajo
        $orden->update([
            'fecha_diagnostico' => now(),
            'kilometraje_ingreso' => $request->kilometraje_ingreso,
            'estado' => 'diagnostico',
        ]);
        
        // Actualizar el kilometraje del vehículo
        $orden->vehiculo->actualizarKilometraje($request->kilometraje_ingreso);
        
        return redirect()->route('admin.mantenimiento.ordenes.show', $orden)
            ->with('success', 'Ingreso del vehículo registrado correctamente');
    }
    
    /**
     * Registrar diagnóstico del vehículo
     */
    public function registrarDiagnostico(Request $request, OrdenTrabajoMantenimiento $orden)
    {
        // Validación de datos
        $request->validate([
            'diagnostico' => 'required|string',
        ]);
        
        // Actualizar la orden de trabajo
        $orden->update([
            'diagnostico' => $request->diagnostico,
            'estado' => 'espera_aprobacion',
        ]);
        
        return redirect()->route('admin.mantenimiento.ordenes.show', $orden)
            ->with('success', 'Diagnóstico registrado correctamente');
    }
    
    /**
     * Registrar aprobación del cliente
     */
    public function registrarAprobacion(Request $request, OrdenTrabajoMantenimiento $orden)
    {
        // Validación de datos
        $request->validate([
            'metodo_aprobacion' => 'required|string',
        ]);
        
        // Actualizar la orden de trabajo
        $orden->update([
            'fecha_aprobacion_cliente' => now(),
            'aprobado_por_cliente' => true,
            'metodo_aprobacion' => $request->metodo_aprobacion,
            'estado' => 'en_progreso',
            'fecha_inicio_trabajo' => now(),
        ]);
        
        return redirect()->route('admin.mantenimiento.ordenes.show', $orden)
            ->with('success', 'Aprobación del cliente registrada correctamente');
    }
    
    /**
     * Finalizar trabajo
     */
    public function finalizarTrabajo(Request $request, OrdenTrabajoMantenimiento $orden)
    {
        // Validación de datos
        $request->validate([
            'recomendaciones' => 'nullable|string',
            'fecha_proxima_revision' => 'nullable|date',
            'kilometraje_salida' => 'required|integer|min:0',
        ]);
        
        // Actualizar la orden de trabajo
        $orden->update([
            'fecha_fin_trabajo' => now(),
            'recomendaciones' => $request->recomendaciones,
            'fecha_proxima_revision' => $request->fecha_proxima_revision,
            'kilometraje_salida' => $request->kilometraje_salida,
            'estado' => 'finalizado',
        ]);
        
        return redirect()->route('admin.mantenimiento.ordenes.show', $orden)
            ->with('success', 'Trabajo finalizado correctamente');
    }
    
    /**
     * Generar factura
     */
    public function generarFactura(Request $request, OrdenTrabajoMantenimiento $orden)
    {
        // Validar si ya existe una factura
        if ($orden->factura) {
            return redirect()->route('admin.mantenimiento.ordenes.show', $orden)
                ->withErrors(['error' => 'Esta orden ya tiene una factura generada']);
        }
        
        // Validación de datos
        $request->validate([
            'numero_factura' => 'required|string|unique:facturas_orden_trabajo,numero_factura',
            'metodo_pago' => 'required|string',
            'dias_garantia' => 'required|integer|min:0',
        ]);
        
        DB::beginTransaction();
        
        try {
            // Calcular subtotal, impuestos y total
            $subtotal = $orden->getTotalOrdenAttribute();
            $impuestos = $subtotal * 0.18; // IGV 18%
            $total = $subtotal + $impuestos;
            
            // Crear la factura
            $factura = FacturaOrdenTrabajo::create([
                'orden_trabajo_id' => $orden->id,
                'numero_factura' => $request->numero_factura,
                'fecha_emision' => now(),
                'subtotal' => $subtotal,
                'impuestos' => $impuestos,
                'total' => $total,
                'metodo_pago' => $request->metodo_pago,
                'estado_pago' => 'pendiente',
                'notas' => $request->notas ?? null,
                'dias_garantia' => $request->dias_garantia,
            ]);
            
            // Actualizar estado de la orden
            $orden->update([
                'estado' => 'facturado',
            ]);
            
            DB::commit();
            
            return redirect()->route('admin.mantenimiento.ordenes.show', $orden)
                ->with('success', 'Factura generada correctamente');
                
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Error al generar la factura: ' . $e->getMessage()]);
        }
    }
    
    /**
     * Registrar pago de factura
     */
    public function registrarPago(Request $request, OrdenTrabajoMantenimiento $orden)
    {
        // Validar si tiene factura
        if (!$orden->factura) {
            return redirect()->route('admin.mantenimiento.ordenes.show', $orden)
                ->withErrors(['error' => 'Esta orden no tiene una factura generada']);
        }
        
        // Validación de datos
        $request->validate([
            'metodo_pago' => 'required|string',
        ]);
        
        // Actualizar la factura
        $orden->factura->update([
            'estado_pago' => 'pagado',
            'metodo_pago' => $request->metodo_pago,
        ]);
        
        // Actualizar estado de la orden
        $orden->update([
            'estado' => 'entregado',
            'fecha_entrega' => now(),
        ]);
        
        return redirect()->route('admin.mantenimiento.ordenes.show', $orden)
            ->with('success', 'Pago registrado correctamente');
    }

    /**
     * Imprimir orden de trabajo
     */
    public function imprimirOrden(OrdenTrabajoMantenimiento $orden)
    {
        $orden->load([
            'cliente.telefonos', 
            'vehiculo.marca', 
            'vehiculo.modelo', 
            'vehiculo.combustible', 
            'tecnico', 
            'detallesRepuestos.parte', 
            'detallesServicios', 
            'factura'
        ]);
        
        return view('admin.mantenimiento.ordenes.print', compact('orden'));
    }

    /**
     * Eliminar repuesto de la orden de trabajo
     */
    public function eliminarRepuesto(DetalleOrdenTrabajoRepuesto $detalle)
    {
        try {
            $orden = $detalle->ordenTrabajo;
            
            // Verificar que la orden esté en un estado que permita modificaciones
            if (in_array($orden->estado, ['facturado', 'entregado'])) {
                return back()->withErrors(['error' => 'No se puede eliminar repuestos de una orden ' . $orden->estado]);
            }
            
            $detalle->delete();
            
            return back()->with('success', 'Repuesto eliminado correctamente');
            
        } catch (\Exception $e) {
            Log::error('Error al eliminar repuesto: ' . $e->getMessage());
            return back()->withErrors(['error' => 'Error al eliminar el repuesto']);
        }
    }

    /**
     * Eliminar servicio de la orden de trabajo
     */
    public function eliminarServicio(DetalleOrdenTrabajoServicio $detalle)
    {
        try {
            $orden = $detalle->ordenTrabajo;
            
            // Verificar que la orden esté en un estado que permita modificaciones
            if (in_array($orden->estado, ['facturado', 'entregado'])) {
                return back()->withErrors(['error' => 'No se puede eliminar servicios de una orden ' . $orden->estado]);
            }
            
            $detalle->delete();
            
            return back()->with('success', 'Servicio eliminado correctamente');
            
        } catch (\Exception $e) {
            Log::error('Error al eliminar servicio: ' . $e->getMessage());
            return back()->withErrors(['error' => 'Error al eliminar el servicio']);
        }
    }

    /**
     * Buscar órdenes de trabajo activas (para el escáner de partes)
     */
    public function buscarActivas(Request $request)
    {
        $q = trim($request->input('q', ''));

        $ordenes = OrdenTrabajoMantenimiento::with(['cliente', 'vehiculo'])
            ->whereNotIn('estado', ['entregado'])
            ->when($q, function ($query) use ($q) {
                $query->where(function ($query2) use ($q) {
                    $like = '%' . $q . '%';
                    $query2->where('codigo_orden', 'like', $like)
                        ->orWhereHas('cliente', function ($q3) use ($like) {
                            $q3->where('nombres', 'like', $like)
                               ->orWhere('apellido_paterno', 'like', $like)
                               ->orWhere('razon_social', 'like', $like)
                               ->orWhere('documento_identidad', 'like', $like);
                        })
                        ->orWhereHas('vehiculo', function ($q3) use ($like) {
                            $q3->where('placa', 'like', $like);
                        });
                });
            })
            ->orderBy('created_at', 'desc')
            ->limit(15)
            ->get();

        return response()->json($ordenes->map(function ($orden) {
            $clienteNombre = '';
            if ($orden->cliente) {
                $clienteNombre = $orden->cliente->tipo_cliente === 'natural'
                    ? trim(($orden->cliente->nombres ?? '') . ' ' . ($orden->cliente->apellido_paterno ?? ''))
                    : ($orden->cliente->razon_social ?? '');
            }
            return [
                'id'            => $orden->id,
                'codigo_orden'  => $orden->codigo_orden,
                'cliente_nombre'=> $clienteNombre ?: 'Sin cliente',
                'placa'         => $orden->vehiculo->placa ?? 'Sin placa',
                'estado'        => $orden->estado,
                'fecha_ingreso' => $orden->fecha_ingreso ? $orden->fecha_ingreso->format('d/m/Y') : '',
            ];
        }));
    }

    /**
     * Agregar múltiples repuestos a una orden de trabajo en un solo envío (desde el escáner)
     */
    public function agregarRepuestosLote(Request $request, OrdenTrabajoMantenimiento $orden)
    {
        $request->validate([
            'items'                   => 'required|array|min:1',
            'items.*.parte_id'        => 'required|exists:partes,id',
            'items.*.cantidad'        => 'required|integer|min:1',
            'items.*.precio_unitario' => 'required|numeric|min:0',
        ]);

        if (in_array($orden->estado, ['facturado', 'entregado'])) {
            return response()->json([
                'success' => false,
                'message' => 'No se pueden agregar repuestos a una orden en estado "' . $orden->estado . '".',
            ], 422);
        }

        DB::beginTransaction();
        try {
            foreach ($request->items as $item) {
                $parte = Parte::findOrFail($item['parte_id']);
                DetalleOrdenTrabajoRepuesto::create([
                    'orden_trabajo_id' => $orden->id,
                    'parte_id'         => $parte->id,
                    'cantidad'         => $item['cantidad'],
                    'precio_unitario'  => $item['precio_unitario'],
                    'descripcion'      => $parte->nombre,
                ]);
            }
            DB::commit();

            return response()->json([
                'success'  => true,
                'message'  => count($request->items) . ' repuesto(s) agregado(s) a la orden ' . $orden->codigo_orden . '.',
                'redirect' => route('admin.mantenimiento.ordenes.show', $orden),
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error en agregarRepuestosLote: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error al agregar repuestos: ' . $e->getMessage(),
            ], 500);
        }
    }
}