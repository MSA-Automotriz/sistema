<?php

namespace App\Http\Controllers\Admin\Ventas;

use App\Http\Controllers\Controller;
use App\Models\Cotizacion;
use App\Models\DetalleCotizacion;
use App\Models\HistorialCotizacion;
use App\Models\EstadoCotizacion;
use App\Models\Cliente;
use App\Models\Parte;
use App\Models\Unidad;
use App\Models\Almacen;
use App\Models\Oportunidad;
use App\Models\Vehiculo;
use App\Models\Color;
use App\Models\Marca;
use App\Models\Modelo;
use App\Models\Version;
use App\Models\AnioModelo;
use App\Models\SeguimientoCotizacion;
use App\Models\SeguimientoOportunidad;
use App\Models\RequerimientoCompra;
use App\Models\DetalleRequerimientoCompra;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;


class CotizacionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    /**
 * Display a listing of the resource.
 */
/**
 * Display a listing of the resource.
 */
public function index(Request $request)
{
    $estados = EstadoCotizacion::all();
    
    $cotizaciones = Cotizacion::with(['cliente', 'estado', 'almacen', 'usuario', 'seguimientos'])
        ->estado($request->estado_id)
        ->cliente($request->cliente_id)
        ->fechaRango($request->fecha_desde, $request->fecha_hasta)
        ->latest()
        ->paginate(10);
    
    // Calcular estadísticas
    $totalCotizaciones = Cotizacion::count();
    
    // Obtener estados clave por nombre
    $estadoGestionado = EstadoCotizacion::where('nombre', 'LIKE', '%EMITIDA%')
        ->orWhere('nombre', 'LIKE', '%PROCESADA%')
        ->first();
    $estadoContactable = EstadoCotizacion::where('nombre', 'LIKE', '%CONTACTABLE%')->first();
    $estadoInteresado = EstadoCotizacion::where('nombre', 'LIKE', '%Interesado%')->first();
    
    // Contar cotizaciones por estado
    $totalGestionados = $estadoGestionado ? Cotizacion::where('estado_id', $estadoGestionado->id)->count() : 0;
    $totalContactables = $estadoContactable ? Cotizacion::where('estado_id', $estadoContactable->id)->count() : 0;
    $totalInteresados = $estadoInteresado ? Cotizacion::where('estado_id', $estadoInteresado->id)->count() : 0;
    
    // Conteo alternativo si no se encuentran los estados específicos
    if ($totalGestionados == 0) {
        // Usar el conteo de cotizaciones con seguimientos como aproximación de cotizaciones gestionadas
        $idsConSeguimientos = DB::table('seguimientos_cotizacion')
            ->select('cotizacion_id')
            ->distinct()
            ->pluck('cotizacion_id');
        
        $totalGestionados = count($idsConSeguimientos);
    }
    
    // Calcular porcentajes
    $porcentajeGestionados = $totalCotizaciones > 0 ? round(($totalGestionados / $totalCotizaciones) * 100) : 0;
    $porcentajeContactables = $totalCotizaciones > 0 ? round(($totalContactables / $totalCotizaciones) * 100) : 0;
    $porcentajeInteresados = $totalCotizaciones > 0 ? round(($totalInteresados / $totalCotizaciones) * 100) : 0;
    
    // Asegurar porcentajes mínimos para el funnel visual
    $porcentajeContactables = max(90, $porcentajeContactables); // Al menos 90% del ancho para visualización
    $porcentajeInteresados = max(70, $porcentajeInteresados);   // Al menos 70% del ancho para visualización
    
    // Calcular estadísticas de prioridad
    // Fechas para determinar si está vencido, por vencer o a tiempo
    $hoy = now();
    $fechaLimite = $hoy->copy()->addDays(3); // 3 días para "por vencer"
    
    // Contar por categoría de tiempo (solo cotizaciones con fecha_validez)
    $vencidos = Cotizacion::whereNotNull('fecha_validez')->where('fecha_validez', '<', $hoy)->count();
    $porVencer = Cotizacion::whereNotNull('fecha_validez')->whereBetween('fecha_validez', [$hoy, $fechaLimite])->count();
    $aTiempo = Cotizacion::whereNotNull('fecha_validez')->where('fecha_validez', '>', $fechaLimite)->count();
    
    // Calcular porcentajes
    $totalConFechas = $vencidos + $porVencer + $aTiempo;
    $porcentajeVencidos = $totalConFechas > 0 ? round(($vencidos / $totalConFechas) * 100) : 0;
    $porcentajePorVencer = $totalConFechas > 0 ? round(($porVencer / $totalConFechas) * 100) : 0;
    $porcentajeATiempo = $totalConFechas > 0 ? round(($aTiempo / $totalConFechas) * 100) : 0;
    
    // Armar array de estadísticas
    $estadisticas = [
        // Estadísticas del funnel
        'totalCotizaciones' => $totalCotizaciones,
        'totalGestionados' => $totalGestionados,
        'totalContactables' => $totalContactables,
        'totalInteresados' => $totalInteresados,
        'porcentajeGestionados' => $porcentajeGestionados,
        'porcentajeContactables' => $porcentajeContactables,
        'porcentajeInteresados' => $porcentajeInteresados,
        
        // Estadísticas de prioridad
        'vencidos' => $vencidos,
        'porVencer' => $porVencer,
        'aTiempo' => $aTiempo,
        'porcentajeVencidos' => $porcentajeVencidos,
        'porcentajePorVencer' => $porcentajePorVencer,
        'porcentajeATiempo' => $porcentajeATiempo,
    ];
        
    return view('admin.ventas.cotizaciones.index', compact('cotizaciones', 'estados', 'estadisticas'));
}

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $almacenes = Almacen::where('es_vehiculos', true)->get();
        $canales = ['Chevy Plan', 'Flota', 'Retail', 'Transferencia', 'Usado'];
        
        return view('admin.ventas.cotizaciones.create', compact('almacenes', 'canales'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'cliente_id' => 'required|exists:clientes,id',
            'almacen_id' => 'required|exists:almacenes,id',
            'condicion' => 'required|in:Nuevo,Usado',
            'canal' => 'required|in:Chevy Plan,Flota,Retail,Transferencia,Usado',
            'moneda' => 'required|in:Soles,Dólares',
            'forma_pago' => 'required|in:Contado,Crédito',
            'datos_adicionales' => 'nullable|string',
            'fecha_validez' => 'nullable|date',
            'tipo_cotizacion' => 'required|in:vehiculos,repuestos,servicios',
            'items.*.vehiculo_catalogo_id' => 'required_if:tipo_cotizacion,vehiculos|exists:vehiculos,id',
        ]);
    
        Log::info('Iniciando creación de cotización', [
            'tipo' => $request->tipo_cotizacion,
            'cliente_id' => $request->cliente_id,
            'almacen_id' => $request->almacen_id
        ]);
    
        // Validar los items según el tipo de cotización
        if ($request->tipo_cotizacion == 'vehiculos' && (!isset($request->items) || count(array_filter($request->items, function($item) { return $item['tipo'] == 'vehiculos'; })) == 0)) {
            Log::warning('Intento de crear cotización sin vehículos');
            return back()->withInput()->withErrors(['error' => 'Debe agregar al menos un vehículo a la cotización']);
        } elseif ($request->tipo_cotizacion == 'repuestos' && (!isset($request->items) || count(array_filter($request->items, function($item) { return $item['tipo'] == 'repuestos'; })) == 0)) {
            Log::warning('Intento de crear cotización sin repuestos');
            return back()->withInput()->withErrors(['error' => 'Debe agregar al menos un repuesto a la cotización']);
        } elseif ($request->tipo_cotizacion == 'servicios' && (!isset($request->items) || count(array_filter($request->items, function($item) { return $item['tipo'] == 'servicios'; })) == 0)) {
            Log::warning('Intento de crear cotización sin servicios');
            return back()->withInput()->withErrors(['error' => 'Debe agregar al menos un servicio a la cotización']);
        }
    
        try {
            DB::beginTransaction();
    
            // Intentar obtener el estado 'Interesado' directamente con ID 1
            $estadoInicial = EstadoCotizacion::find(1);
            
            // Si no existe el ID 1, intentar buscar por nombre 'Interesado'
            if (!$estadoInicial) {
                $estadoInicial = EstadoCotizacion::where('nombre', 'Interesado')->first();
            }
            
            // Si aún no existe, obtener el primer estado disponible
            if (!$estadoInicial) {
                $estadoInicial = EstadoCotizacion::first();
                
                // Si no hay estados configurados, devolver error
                if (!$estadoInicial) {
                    Log::error('No hay estados de cotización configurados en la base de datos');
                    throw new \Exception("No hay estados de cotización configurados en la base de datos. Por favor, configure al menos un estado.");
                }
            }
    
            Log::info('Estado inicial encontrado', ['id' => $estadoInicial->id, 'nombre' => $estadoInicial->nombre]);
    
            // Crear la cotización
            $cotizacion = Cotizacion::create([
                'codigo' => Cotizacion::generarCodigo(),
                'cliente_id' => $request->cliente_id,
                'almacen_id' => $request->almacen_id,
                'condicion' => $request->condicion,
                'canal' => $request->canal,
                'moneda' => $request->moneda,
                'forma_pago' => $request->forma_pago,
                'datos_adicionales' => $request->datos_adicionales,
                'fecha_validez' => $request->fecha_validez,
                'estado_id' => $estadoInicial->id, 
                'user_id' => Auth::id(),
            ]);
    
            Log::info('Cotización creada', ['id' => $cotizacion->id]);
    
            // Procesar los items según el tipo de cotización
            $subtotal = 0;
    
            if ($request->tipo_cotizacion == 'vehiculos' && isset($request->items)) {
                Log::info('Procesando items de vehículos', ['count' => count($request->items)]);
    
                foreach ($request->items as $item) {
                    if ($item['tipo'] != 'vehiculos') continue;
    
                    // Verificar si existe el vehículo y el color
                    $vehiculo = Vehiculo::find($item['item_id']);
                    if (!$vehiculo) {
                        Log::error('Vehículo no encontrado', ['id' => $item['item_id']]);
                        throw new \Exception("Vehículo con ID {$item['item_id']} no encontrado");
                    }
    
                    $color = Color::find($item['color_id'] ?? null);
                    if (!$color) {
                        $color = Color::first(); // Obtener el primer color como predeterminado
                        if (!$color) {
                            throw new \Exception("No hay colores disponibles en el sistema. Por favor, configure al menos un color.");
                        }
                        Log::warning('Color no especificado, utilizando color por defecto', ['color_id' => $color->id]);
                    }
    
                    $precioUnitario = $item['precio_unitario'];
                    $cantidad = $item['cantidad'];
                    $descuento = $item['descuento'] ?? 0;
    
                    $detSubtotal = $precioUnitario * $cantidad;
                    $detTotal = $detSubtotal * (1 - $descuento / 100);
    
                    // Crear el detalle de cotización SIN vehiculo_id
                    $detalle = DetalleCotizacion::create([
                        'cotizacion_id' => $cotizacion->id,
                        'vehiculo_catalogo_id' => $item['item_id'],
                        'color_id' => $color->id,
                        'cantidad' => $cantidad,
                        'precio_unitario' => $precioUnitario,
                        'descuento' => $descuento,
                        'subtotal' => $detSubtotal,
                        'total' => $detTotal,
                    ]);
    
                    Log::info('Detalle de vehículo agregado', [
                        'id' => $detalle->id,
                        'vehiculo_catalogo_id' => $detalle->vehiculo_catalogo_id
                    ]);
    
                    $subtotal += $detTotal;
                }
            } elseif ($request->tipo_cotizacion == 'repuestos' && isset($request->items)) {
                Log::info('Procesando items de repuestos', ['count' => count($request->items)]);
    
                foreach ($request->items as $item) {
                    if ($item['tipo'] != 'repuestos') continue;
    
                    // Verificar si existe el repuesto
                    $repuesto = Parte::find($item['item_id']);
                    if (!$repuesto) {
                        Log::error('Repuesto no encontrado', ['id' => $item['item_id']]);
                        throw new \Exception("Repuesto con ID {$item['item_id']} no encontrado");
                    }
    
                    $precioUnitario = $item['precio_unitario'];
                    $cantidad = $item['cantidad'];
                    $descuento = $item['descuento'] ?? 0;
    
                    $detSubtotal = $precioUnitario * $cantidad;
                    $detTotal = $detSubtotal * (1 - $descuento / 100);
    
                    // Crear el detalle de cotización para repuesto
                    $detalle = DetalleCotizacion::create([
                        'cotizacion_id' => $cotizacion->id,
                        'repuesto_id' => $item['item_id'],
                        'cantidad' => $cantidad,
                        'precio_unitario' => $precioUnitario,
                        'descuento' => $descuento,
                        'subtotal' => $detSubtotal,
                        'total' => $detTotal,
                    ]);
    
                    Log::info('Detalle de repuesto agregado', ['id' => $detalle->id]);
    
                    $subtotal += $detTotal;
                }
            } elseif ($request->tipo_cotizacion == 'servicios' && isset($request->items)) {
                Log::info('Procesando items de servicios', ['count' => count($request->items)]);
    
                foreach ($request->items as $item) {
                    if ($item['tipo'] != 'servicios') continue;
    
                    // Verificar si existe el servicio
                    $servicio = Servicios::find($item['item_id']);
                    if (!$servicio) {
                        Log::error('Servicio no encontrado', ['id' => $item['item_id']]);
                        throw new \Exception("Servicio con ID {$item['item_id']} no encontrado");
                    }
    
                    $precioUnitario = $item['precio_unitario'];
                    $cantidad = $item['cantidad'];
                    $descuento = $item['descuento'] ?? 0;
    
                    $detSubtotal = $precioUnitario * $cantidad;
                    $detTotal = $detSubtotal * (1 - $descuento / 100);
    
                    // Crear el detalle de cotización para servicio
                    $detalle = DetalleCotizacion::create([
                        'cotizacion_id' => $cotizacion->id,
                        'servicio_id' => $item['item_id'],
                        'cantidad' => $cantidad,
                        'precio_unitario' => $precioUnitario,
                        'descuento' => $descuento,
                        'subtotal' => $detSubtotal,
                        'total' => $detTotal,
                    ]);
    
                    Log::info('Detalle de servicio agregado', ['id' => $detalle->id]);
    
                    $subtotal += $detTotal;
                }
            }
    
            // Calcular impuestos (IGV 18%)
            $impuestos = $subtotal * 0.18;
            $total = $subtotal + $impuestos;
    
            // Actualizar los totales de la cotización
            $cotizacion->update([
                'subtotal' => $subtotal,
                'impuestos' => $impuestos,
                'total' => $total,
            ]);
    
            Log::info('Cotización actualizada con totales', [
                'subtotal' => $subtotal,
                'impuestos' => $impuestos,
                'total' => $total
            ]);
    
            // Registrar en historial
            HistorialCotizacion::create([
                'cotizacion_id' => $cotizacion->id,
                'estado_anterior_id' => null,
                'estado_nuevo_id' => $estadoInicial->id,
                'user_id' => Auth::id(),
                'comentario' => "Cotización creada inicialmente en estado {$estadoInicial->nombre}",
            ]);
    
            Log::info('Historial de cotización registrado');
    
            DB::commit();
    
            Log::info('Cotización creada exitosamente', ['id' => $cotizacion->id]);
    
            return redirect()->route('admin.ventas.cotizaciones.index')
            ->with('success', 'Cotización creada exitosamente');
    } catch (\Exception $e) {
        DB::rollBack();
        Log::error("Error al crear cotización: " . $e->getMessage(), [
            'file' => $e->getFile(),
            'line' => $e->getLine(),
            'trace' => $e->getTraceAsString()
        ]);
        return back()->withInput()->withErrors(['error' => 'Error al crear la cotización: ' . $e->getMessage()]);
    }
}

    /**
     * Display the specified resource.
     */
    public function show(Cotizacion $cotizacion)
    {
        $cotizacion->load([
            'cliente',
            'almacen',
            'estado',
            'usuario',
            'detalles' => function($query) {
                $query->with([
                    'vehiculo' => function($q) {
                        $q->with(['marca', 'modelo', 'version']);
                    },
                    'vehiculoCatalogo' => function($q) {
                        $q->with(['marca', 'modelo', 'version', 'anioModelo']);
                    },
                    'color'
                ]);
            },
            'historial.usuario',
            'historial.estadoAnterior',
            'historial.estadoNuevo',
            'seguimientos.usuario'
        ]);
    
        $estadoConvertidaId = EstadoCotizacion::where('nombre', 'Convertida')->value('id');
        $estados = EstadoCotizacion::all(); 
       
        return view('admin.ventas.cotizaciones.show', [
            'cotizacion' => $cotizacion,
            'estadoConvertidaId' => $estadoConvertidaId,
            'estados' => $estados
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Cotizacion $cotizacion)
    {
        // Se puede editar si no está en un estado final como Facturada o Cerrada
        $estadosBloqueados = ['Facturada', 'Cerrada', 'Convertida'];
        if ($cotizacion->estado && in_array($cotizacion->estado->nombre, $estadosBloqueados)) {
            return redirect()->route('admin.ventas.cotizaciones.show', $cotizacion)
                ->with('error', 'No se pueden editar cotizaciones que ya están ' . $cotizacion->estado->nombre);
        }
        
        $cotizacion->load(['cliente', 'almacen', 'detalles.vehiculo', 'detalles.color']);
        $almacenes = Almacen::where('es_vehiculos', true)->get();
        $canales = ['Chevy Plan', 'Flota', 'Retail', 'Transferencia', 'Usado'];
        
        return view('admin.ventas.cotizaciones.edit', compact('cotizacion', 'almacenes', 'canales'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Cotizacion $cotizacion)
    {
        $estadosBloqueados = ['Facturada', 'Cerrada', 'Convertida'];
        if ($cotizacion->estado && in_array($cotizacion->estado->nombre, $estadosBloqueados)) {
            return redirect()->route('admin.ventas.cotizaciones.show', $cotizacion)
                ->with('error', 'No se pueden actualizar cotizaciones que ya están ' . $cotizacion->estado->nombre);
        }
        
        $request->validate([
            'cliente_id' => 'required|exists:clientes,id',
            'almacen_id' => 'required|exists:almacenes,id',
            'condicion' => 'required|in:Nuevo,Usado',
            'canal' => 'required|in:Chevy Plan,Flota,Retail,Transferencia,Usado',
            'moneda' => 'required|in:Soles,Dólares',
            'forma_pago' => 'required|in:Contado,Crédito',
            'datos_adicionales' => 'nullable|string',
            'fecha_validez' => 'nullable|date',
            'vehiculos' => 'required|array|min:1',
            'vehiculos.*.id' => 'nullable|exists:detalles_cotizacion,id',
            'vehiculos.*.vehiculo_catalogo_id' => 'required|exists:catalogos,id',
            'vehiculos.*.color_id' => 'required|exists:colores,id',
            'vehiculos.*.precio_unitario' => 'required|numeric|min:0',
            'vehiculos.*.descuento' => 'nullable|numeric|min:0|max:100',
            'vehiculos.*.cantidad' => 'required|integer|min:1',
        ]);
    
        try {
            DB::beginTransaction();
            
            // Actualizar datos básicos de la cotización
            $cotizacion->update([
                'cliente_id' => $request->cliente_id,
                'almacen_id' => $request->almacen_id,
                'condicion' => $request->condicion,
                'canal' => $request->canal,
                'moneda' => $request->moneda,
                'forma_pago' => $request->forma_pago,
                'datos_adicionales' => $request->datos_adicionales,
                'fecha_validez' => $request->fecha_validez,
            ]);
            
            // Procesar los vehículos seleccionados
            $subtotal = 0;
            $requerirCompra = false;
            $detallesRequerimiento = [];
            
            // Obtener los IDs de los detalles actualizados para identificar los eliminados
            $detallesIds = [];
            
            foreach ($request->vehiculos as $vehiculoData) {
                $precioUnitario = $vehiculoData['precio_unitario'];
                $cantidad = $vehiculoData['cantidad'];
                $descuento = $vehiculoData['descuento'] ?? 0;
                
                $detSubtotal = $precioUnitario * $cantidad;
                $detTotal = $detSubtotal * (1 - $descuento / 100);
                
                // Actualizar o crear el detalle
                if (isset($vehiculoData['id'])) {
                    // Actualizar detalle existente
                    $detalle = DetalleCotizacion::find($vehiculoData['id']);
                    if ($detalle && $detalle->cotizacion_id == $cotizacion->id) {
                        $detalle->update([
                            'vehiculo_catalogo_id' => $vehiculoData['vehiculo_catalogo_id'],
                            'color_id' => $vehiculoData['color_id'],
                            'cantidad' => $cantidad,
                            'precio_unitario' => $precioUnitario,
                            'descuento' => $descuento,
                            'subtotal' => $detSubtotal,
                            'total' => $detTotal,
                        ]);
                        $detallesIds[] = $detalle->id;
                    }
                } else {
                    // Crear nuevo detalle
                    $detalle = DetalleCotizacion::create([
                        'cotizacion_id' => $cotizacion->id,
                        'vehiculo_catalogo_id' => $vehiculoData['vehiculo_catalogo_id'],
                        'color_id' => $vehiculoData['color_id'],
                        'cantidad' => $cantidad,
                        'precio_unitario' => $precioUnitario,
                        'descuento' => $descuento,
                        'subtotal' => $detSubtotal,
                        'total' => $detTotal,
                    ]);
                    $detallesIds[] = $detalle->id;
                }
                
                $subtotal += $detTotal;
                
                // Verificar si se requiere crear un requerimiento de compra (sin stock)
                if (isset($vehiculoData['sin_stock']) && $vehiculoData['sin_stock']) {
                    $requerirCompra = true;
                    $Vehiculo = Vehiculo::with(['marca', 'modelo', 'version', 'anioModelo'])->find($vehiculoData['vehiculo_catalogo_id']);
                    $color = Color::find($vehiculoData['color_id']);
                    
                    $detallesRequerimiento[] = [
                        'vehiculo_catalogo_id' => $vehiculoData['vehiculo_catalogo_id'],
                        'color_id' => $vehiculoData['color_id'],
                        'cantidad' => $cantidad,
                        'descripcion' => "Vehículo {$Vehiculo->marca->nombre} {$Vehiculo->modelo->nombre} {$Vehiculo->version->nombre} {$Vehiculo->anioModelo->nombre} - Color {$color->nombre}",
                    ];
                }
            }
            
            // Eliminar detalles que ya no están en la solicitud
            DetalleCotizacion::where('cotizacion_id', $cotizacion->id)
                ->whereNotIn('id', $detallesIds)
                ->delete();
            
            // Calcular impuestos (IGV 18%)
            $impuestos = $subtotal * 0.18;
            $total = $subtotal + $impuestos;
            
            // Actualizar los totales de la cotización
            $cotizacion->update([
                'subtotal' => $subtotal,
                'impuestos' => $impuestos,
                'total' => $total,
            ]);
            
            // Crear requerimiento de compra si es necesario
            if ($requerirCompra && !empty($detallesRequerimiento)) {
                $estadoRequerimiento = DB::table('estados')->where('nombre', 'Pendiente')->first();
                
                $requerimiento = RequerimientoCompra::create([
                    'tipo' => 'Vehículo',
                    'almacen_id' => $request->almacen_id,
                    'comentario' => "Requerimiento automático generado desde cotización {$cotizacion->codigo} (Actualización)",
                    'estado_id' => $estadoRequerimiento ? $estadoRequerimiento->id : 1,
                    'user_id' => Auth::id(),
                ]);
                
                foreach ($detallesRequerimiento as $detalleData) {
                    DetalleRequerimientoCompra::create([
                        'requerimiento_compra_id' => $requerimiento->id,
                        'vehiculo_catalogo_id' => $detalleData['vehiculo_catalogo_id'],
                        'color_id' => $detalleData['color_id'],
                        'cantidad' => $detalleData['cantidad'],
                        'descripcion' => $detalleData['descripcion'],
                    ]);
                }
            }
            
            DB::commit();
            
            return redirect()->route('admin.ventas.cotizaciones.show', $cotizacion)
                ->with('success', 'Cotización actualizada exitosamente');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Error al actualizar cotización: " . $e->getMessage());
            return back()->withInput()->withErrors(['error' => 'Error al actualizar la cotización: ' . $e->getMessage()]);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Cotizacion $cotizacion)
    {
        $estadosBloqueados = ['Facturada', 'Cerrada', 'Convertida'];
        if ($cotizacion->estado && in_array($cotizacion->estado->nombre, $estadosBloqueados)) {
            return redirect()->route('admin.ventas.cotizaciones.index')
                ->with('error', 'No se pueden eliminar cotizaciones que ya están ' . $cotizacion->estado->nombre);
        }
        
        try {
            DB::beginTransaction();
            
            // Eliminar detalles (se hará automáticamente por la relación onDelete cascade)
            // Eliminar historial (se hará automáticamente por la relación onDelete cascade)
            
            // Eliminar la cotización
            $cotizacion->delete();
            
            DB::commit();
            
            return redirect()->route('admin.ventas.cotizaciones.index')
                ->with('success', 'Cotización eliminada exitosamente');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Error al eliminar cotización: " . $e->getMessage());
            return back()->withErrors(['error' => 'Error al eliminar la cotización: ' . $e->getMessage()]);
        }
    }


/**
 * Buscar clientes para autocompletado AJAX
 */
public function buscarClientesAjax(Request $request)
{
    $term = $request->input('term', '');
    
    $clientes = Cliente::where(function($query) use ($term) {
            $query->where('tipo_cliente', 'natural')
                ->whereRaw("CONCAT(nombres, ' ', apellido_paterno) LIKE ?", ["%$term%"]);
        })
        ->orWhere(function($query) use ($term) {
            $query->where('tipo_cliente', 'juridico')
                ->where('razon_social', 'LIKE', "%$term%");
        })
        ->orWhere('documento_identidad', 'LIKE', "%$term%")
        ->take(10)
        ->get()
        ->map(function ($cliente) {
            return [
                'id' => $cliente->id,
                'tipo_cliente' => $cliente->tipo_cliente,
                'nombres' => $cliente->tipo_cliente === 'natural' ? $cliente->nombres : null,
                'apellido_paterno' => $cliente->tipo_cliente === 'natural' ? $cliente->apellido_paterno : null,
                'apellido_materno' => $cliente->tipo_cliente === 'natural' ? $cliente->apellido_materno : null,
                'razon_social' => $cliente->tipo_cliente === 'juridico' ? $cliente->razon_social : null,
                'documento_identidad' => $cliente->documento_identidad
            ];
        });

    return response()->json($clientes);
}
public function buscarClientes(Request $request)
    {
        $term = $request->input('term', '');
        $clientes = Cliente::where('tipo_cliente', 'natural')
            ->whereRaw("CONCAT(nombres, ' ', apellido_paterno) LIKE ?", ["%$term%"])
            ->orWhere('tipo_cliente', 'juridico')
            ->where('razon_social', 'LIKE', "%$term%")
            ->take(10)
            ->get()
            ->map(function ($cliente) {
                return [
                    'id' => $cliente->id,
                    'text' => $cliente->tipo_cliente === 'natural'
                        ? $cliente->nombres . ' ' . $cliente->apellido_paterno
                        : $cliente->razon_social
                ];
            });

        return response()->json($clientes);
    }

/**
 * Buscar vehículos del catálogo
 */
public function buscarVehiculos(Request $request)
{
    $term = $request->input('term', '');
    
    if (empty($term)) {
        return response()->json([]);
    }
    
    $vehiculos = Vehiculo::with(['marca', 'modelo', 'version', 'anioModelo'])
        ->whereHas('marca', function($query) use ($term) {
            $query->where('nombre', 'LIKE', "%{$term}%");
        })
        ->orWhereHas('modelo', function($query) use ($term) {
            $query->where('nombre', 'LIKE', "%{$term}%");
        })
        ->orWhereHas('version', function($query) use ($term) {
            $query->where('nombre', 'LIKE', "%{$term}%");
        })
        ->orWhereHas('anioModelo', function($query) use ($term) {
            $query->where('nombre', 'LIKE', "%{$term}%");
        })
        ->limit(10)
        ->get();
        
    $results = [];
    
    foreach ($vehiculos as $vehiculo) {
        $descripcion = ($vehiculo->marca ? $vehiculo->marca->nombre : '') . ' ' .
               ($vehiculo->modelo ? $vehiculo->modelo->nombre : '') . ' ' .
               ($vehiculo->version ? $vehiculo->version->nombre : '') . ' (' .
               ($detalle->color ? $detalle->color->nombre : 'Color no especificado') . ')';
        
        $results[] = [
            'id' => $vehiculo->id,
            'label' => $descripcion,
            'value' => $descripcion,
            'marca' => $vehiculo->marca->nombre,
            'modelo' => $vehiculo->modelo->nombre,
            'version' => $vehiculo->version->nombre,
            'anio' => $vehiculo->anioModelo->nombre,
        ];
    }
    
    return response()->json($results);
}

/**
 * Obtener colores disponibles para un vehículo
 */
public function coloresVehiculo(Request $request, $vehiculoId)
{
    // En un sistema real, aquí consultarías los colores disponibles específicamente para este vehículo
    // Por simplicidad, devolveremos todos los colores
    $colores = Color::all();
    
    return response()->json($colores);
}
/**
 * Muestra el dashboard de ventas (tipo Trello)
 */
public function dashboardVentas()
{
    // Obtener todos los estados de cotización
    $estados = EstadoCotizacion::all();

    // Obtener cotizaciones agrupadas por estado
    $cotizacionesPorEstado = [];
    foreach ($estados as $estado) {
        $cotizaciones = Cotizacion::with(['cliente', 'usuario', 'detalles', 'seguimientos'])
            ->where('estado_id', $estado->id)
            ->latest()
            ->take(50)
            ->get();
        $cotizacionesPorEstado[$estado->id] = [
            'estado' => $estado,
            'cotizaciones' => $cotizaciones,
        ];
    }

    // Obtener oportunidades
    $oportunidades = Oportunidad::with(['cliente', 'usuario', 'seguimientos'])
        ->where('estado', 'Activa')
        ->latest()
        ->take(50)
        ->get();

    return view('admin.ventas.cotizaciones.dashsales', compact('cotizacionesPorEstado', 'oportunidades'));
}

public function manageColumn(Request $request)
{
    $request->validate([
        'nombre' => 'required|string|max:255',
        'color' => 'required|string|in:primary,secondary,success,danger,warning,info',
        'column_id' => 'nullable|exists:estados_cotizacion,id',
    ]);

    try {
        DB::beginTransaction();

        if ($request->column_id) {
            // Actualizar columna existente
            $estado = EstadoCotizacion::findOrFail($request->column_id);
            $estado->update([
                'nombre' => $request->nombre,
                'color' => $request->color,
            ]);
        } else {
            // Crear nueva columna
            EstadoCotizacion::create([
                'nombre' => $request->nombre,
                'color' => $request->color,
            ]);
        }

        DB::commit();
        return response()->json(['success' => true]);
    } catch (\Exception $e) {
        DB::rollBack();
        Log::error("Error al gestionar columna: " . $e->getMessage());
        return response()->json(['error' => 'Error al gestionar la columna'], 500);
    }
}

    public function deleteColumn($id)
    {
        try {
            DB::beginTransaction();
            $estado = EstadoCotizacion::findOrFail($id);
            // Evitar eliminar estados predeterminados como "Nueva"
            if ($estado->nombre === 'Nueva') {
                return response()->json(['error' => 'No se puede eliminar el estado predeterminado'], 400);
            }
            $estado->delete();
            DB::commit();
            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Error al eliminar columna: " . $e->getMessage());
            return response()->json(['error' => 'Error al eliminar la columna'], 500);
        }
    }
/**
 * Agregar seguimiento a una cotización
 */
public function agregarSeguimiento(Request $request, Cotizacion $cotizacion)
{
    $request->validate([
        'tipo' => 'required|in:nota,llamada,reunion,email,otro',
        'contenido' => 'required|string',
        'fecha_seguimiento' => 'required|date',
        'recordatorio' => 'nullable|boolean',
        'fecha_recordatorio' => 'nullable|required_if:recordatorio,1|date|after:fecha_seguimiento',
    ]);
    
    // Registrar el seguimiento (una sola vez)
    $seguimiento = SeguimientoCotizacion::create([
        'cotizacion_id' => $cotizacion->id,
        'user_id' => Auth::id(),
        'tipo' => $request->tipo,
        'contenido' => $request->contenido,
        'fecha_seguimiento' => $request->fecha_seguimiento,
        'recordatorio' => $request->recordatorio ?? false,
        'fecha_recordatorio' => $request->recordatorio ? $request->fecha_recordatorio : null,
    ]);
    
    // Actualizar el estado de cotización si es necesario
    if ($request->has('estado_id')) {
        $cotizacion->update([
            'estado_id' => $request->estado_id
        ]);
    }
    
    // Si la solicitud es AJAX, devolver respuesta JSON
    if ($request->ajax()) {
        return response()->json([
            'success' => true,
            'seguimiento' => $seguimiento->load('usuario'),
        ]);
    }
    
    // Registrar el evento para depuración
    Log::info('Seguimiento agregado correctamente', [
        'cotizacion_id' => $cotizacion->id,
        'seguimiento_id' => $seguimiento->id,
        'tipo' => $request->tipo,
    ]);
    
    // Redireccionar de vuelta con mensaje de éxito
    return redirect()->back()->with('success', 'Seguimiento agregado correctamente');
}

//dependencias
/**
 * Buscar repuestos
 */
public function buscarRepuestos(Request $request)
{
    try {
        $term = $request->input('term', '');
        
        if (empty($term)) {
            return response()->json([]);
        }
        
        $repuestos = Parte::where('codigo', 'LIKE', "%{$term}%")
            ->orWhere('nombre', 'LIKE', "%{$term}%")
            ->limit(10)
            ->get();
            
        $results = [];
        foreach ($repuestos as $repuesto) {
            $unidad = $repuesto->unidad ? $repuesto->unidad->nombre : 'N/A';
            $precio = $repuesto->precio_venta ?? 0;
            
            $results[] = [
                'id' => $repuesto->id,
                'text' => "{$repuesto->codigo} - {$repuesto->nombre}",
                'unidad' => $unidad,
                'precio' => $precio
            ];
        }
        
        return response()->json($results);
    } catch (\Exception $e) {
        \Log::error('Error en buscarRepuestos: ' . $e->getMessage());
        return response()->json(['error' => 'Error al buscar repuestos: ' . $e->getMessage()], 500);
    }
}

/**
 * Buscar servicios
 */
public function buscarServicios(Request $request)
{
    $term = $request->input('term', '');
    
    if (empty($term)) {
        return response()->json([]);
    }
    
    $servicios = Servicios::where('nombre', 'LIKE', "%{$term}%")
        ->limit(10)
        ->get();
        
    return response()->json($servicios);
}

public function searchMarcas(Request $request)
    {
        $term = $request->input('term', '');

        $marcas = Marca::where('nombre', 'LIKE', "%{$term}%")
            ->orderBy('nombre')
            ->limit(10)
            ->get()
            ->map(function ($marca) {
                return [
                    'id' => $marca->id,
                    'text' => $marca->nombre
                ];
            });

        return response()->json($marcas);
    }

    /**
     * Buscar modelos por marca para autocompletado AJAX en cotizaciones
     */
    public function searchModelos(Request $request, $marcaId)
    {
        $term = $request->input('term', '');

        $modelos = Modelo::where('marca_id', $marcaId)
            ->where('nombre', 'LIKE', "%{$term}%")
            ->orderBy('nombre')
            ->limit(10)
            ->get()
            ->map(function ($modelo) {
                return [
                    'id' => $modelo->id,
                    'text' => $modelo->nombre
                ];
            });

        return response()->json($modelos);
    }

    /**
     * Buscar versiones por modelo para autocompletado AJAX en cotizaciones
     */
    public function searchVersiones(Request $request, $modeloId)
    {
        $term = $request->input('term', '');

        $versiones = Version::where('modelo_id', $modeloId)
            ->where('nombre', 'LIKE', "%{$term}%")
            ->orderBy('nombre')
            ->limit(10)
            ->get()
            ->map(function ($version) {
                return [
                    'id' => $version->id,
                    'text' => $version->nombre
                ];
            });

        return response()->json($versiones);
    }

    /**
     * Buscar años por versión para autocompletado AJAX en cotizaciones
     */
    public function searchAnios(Request $request, $versionId)
    {
        $term = $request->input('term', '');

        $anios = AnioModelo::where('version_id', $versionId)
            ->where('anio', 'LIKE', "%{$term}%")
            ->orderBy('anio', 'desc')
            ->limit(10)
            ->get()
            ->map(function ($anio) {
                return [
                    'id' => $anio->id,
                    'text' => $anio->anio,
                    'precio' => $anio->precio_venta ?? 0
                ];
            });

        return response()->json($anios);
    }

    /**
     * Buscar colores para autocompletado AJAX en cotizaciones
     */
    public function searchColores(Request $request)
    {
        $term = $request->input('term', '');

        $colores = Color::where('nombre', 'LIKE', "%{$term}%")
            ->orderBy('nombre')
            ->limit(10)
            ->get()
            ->map(function ($color) {
                return [
                    'id' => $color->id,
                    'text' => $color->nombre,
                    'color' => $color->hexadecimal
                ];
            });

        return response()->json($colores);
    }
    /**
     * Mostrar el formulario para gestionar una cotización
     */
    public function gestionar(Cotizacion $cotizacion)
    {
        $cotizacion->load([
            'cliente',
            'cliente.telefonos',
            'almacen',
            'estado',
            'usuario',
            'detalles.vehiculo',
            'detalles.color',
            'seguimientos.usuario',
            'nota_pedido',
            'orden_trabajo',
            'acta_entrega',
            'documentos_sunarp',
            'placa_info',
            'documentos_placa',
            'documentos',
        ]);
    
        $estados = \App\Models\EstadoCotizacion::all(); // Cargar los estados
    
        \Log::info('Cargando datos para gestionar cotización', [
            'cotizacion_id' => $cotizacion->id,
            'has_orden_trabajo' => $cotizacion->orden_trabajo ? true : false,
            'has_acta_entrega' => $cotizacion->acta_entrega ? true : false,
            'documentos_sunarp_count' => $cotizacion->documentos_sunarp->count(),
            'has_placa_info' => $cotizacion->placa_info ? true : false,
            'documentos_placa_count' => $cotizacion->documentos_placa->count(),
            'documentos_count' => $cotizacion->documentos->count(),
        ]);
    
        return view('admin.ventas.cotizaciones.gestionar', [
            'cotizacion' => $cotizacion,
            'estados' => $estados,
        ]);
    }

    /**
     * Actualizar la gestión de una cotización
     */
    public function actualizarGestion(Request $request, Cotizacion $cotizacion)
    {
        $request->validate([
            'gestion' => 'required|in:si,no',
            'estado_id' => 'required|exists:estados_cotizacion,id',
            'tipo' => 'required|in:nota,llamada,reunion,email,otro',
            'contenido' => 'required|string',
            'fecha_seguimiento' => 'required|date',
        ]);
        
        try {
            DB::beginTransaction();
            
            // Update management status
            $cotizacion->update([
                'gestionado' => $request->gestion === 'si',
                'estado_id' => $request->estado_id
            ]);
            
            // Create tracking record
            $seguimiento = SeguimientoCotizacion::create([
                'cotizacion_id' => $cotizacion->id,
                'user_id' => Auth::id(),
                'tipo' => $request->tipo,
                'contenido' => $request->contenido,
                'fecha_seguimiento' => $request->fecha_seguimiento,
            ]);
            
            DB::commit();
            
            return response()->json([
                'success' => true,
                'gestionado' => $cotizacion->gestionado,
                'estado_id' => $cotizacion->estado_id,
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Error al actualizar gestión de cotización: " . $e->getMessage());
            return response()->json([
                'error' => 'Error al actualizar la gestión: ' . $e->getMessage()
            ], 500);
        }
    }


/**
 * Genera un requerimiento de compra a partir de una cotización con estado "Cerrado Ganado"
 * 
 * @param Request $request
 * @param Cotizacion $cotizacion
 * @return \Illuminate\Http\RedirectResponse
 */
public function generarRequerimiento(Request $request, Cotizacion $cotizacion)
{
    // Validar que la cotización esté en estado "Cerrado Ganado"
    if (!$cotizacion->estado || $cotizacion->estado->nombre !== 'Cerrado Ganado') {
        return redirect()->back()->with('error', 'Solo se pueden generar requerimientos para cotizaciones en estado "Cerrado Ganado"');
    }
    
    // Validar que no exista un requerimiento previo
    if ($cotizacion->requerimientoCompra) {
        return redirect()->back()->with('error', 'Ya existe un requerimiento de compra para esta cotización');
    }
    
    // Validar datos del formulario
    $request->validate([
        'items' => 'required|array|min:1',
        'items.*' => 'exists:detalles_cotizacion,id',
        'comentario' => 'nullable|string|max:1000',
        'prioridad' => 'required|in:Normal,Alta,Urgente',
        'estado_id' => 'required|exists:estados,id', // Adaptar según la tabla que uses para estados
    ]);
    
    try {
        DB::beginTransaction();
        
        // Crear el requerimiento de compra
        $requerimiento = new RequerimientoCompra();
        $requerimiento->cotizacion_id = $cotizacion->id;
        $requerimiento->tipo = 'inventario';
        $requerimiento->almacen_id = $cotizacion->almacen_id;
        $requerimiento->comentario = $request->comentario ?? "Requerimiento generado automáticamente desde cotización {$cotizacion->codigo}";
        $requerimiento->prioridad = $request->prioridad;
        $requerimiento->estado_id = $request->estado_id;
        $requerimiento->user_id = Auth::id();
        $requerimiento->save();
        
        Log::info('Requerimiento creado', ['id' => $requerimiento->id]);
        
        // Procesar detalles seleccionados
        $detallesIds = $request->items;
        $detalles = DetalleCotizacion::whereIn('id', $detallesIds)
                    ->where('cotizacion_id', $cotizacion->id)
                    ->get();
        
        foreach ($detalles as $detalle) {
            // Determinar el tipo de detalle y mapear a la estructura existente
            if ($detalle->vehiculo_catalogo_id) {
                // Es un vehículo del catálogo
                $vehiculo = $detalle->vehiculoCatalogo;
                if (!$vehiculo) {
                    continue; // Saltar si no existe
                }
                
                // Construir descripción de manera segura
                $marcaNombre = $vehiculo->marca ? $vehiculo->marca->nombre : '';
                $modeloNombre = $vehiculo->modelo ? $vehiculo->modelo->nombre : '';
                $versionNombre = $vehiculo->version ? $vehiculo->version->nombre : '';
                $colorNombre = $detalle->color ? $detalle->color->nombre : 'Color no especificado';
                
                $descripcion = "$marcaNombre $modeloNombre $versionNombre ($colorNombre)";
                
                DetalleRequerimientoCompra::create([
                    'requerimiento_compra_id' => $requerimiento->id,
                    'item_id' => $detalle->vehiculo_catalogo_id,
                    'tipo_item' => 'vehiculo',
                    'cantidad' => $detalle->cantidad,
                    'descripcion' => $descripcion,
                    'color_id' => $detalle->color_id,
                    'cotizacion_detalle_id' => $detalle->id,
                ]);
                
            } elseif ($detalle->repuesto_id) {
                // Es un repuesto
                $repuesto = $detalle->repuesto;
                if (!$repuesto) {
                    continue; // Saltar si no existe
                }
                
                // Construir descripción de manera segura
                $codigo = $repuesto->codigo ? $repuesto->codigo : 'N/A';
                $nombre = $repuesto->nombre ? $repuesto->nombre : 'Repuesto sin nombre';
                
                DetalleRequerimientoCompra::create([
                    'requerimiento_compra_id' => $requerimiento->id,
                    'item_id' => $detalle->repuesto_id,
                    'tipo_item' => 'parte',
                    'cantidad' => $detalle->cantidad,
                    'descripcion' => "$codigo - $nombre",
                    'cotizacion_detalle_id' => $detalle->id,
                ]);
                
            } elseif ($detalle->servicio_id) {
                // Es un servicio
                $servicio = $detalle->servicio;
                if (!$servicio) {
                    continue; // Saltar si no existe
                }
                
                // Construir descripción de manera segura
                $nombreServicio = $servicio->nombre ? $servicio->nombre : 'Servicio sin nombre';
                
                // Para servicios, crearemos un registro especial con descripción
                DetalleRequerimientoCompra::create([
                    'requerimiento_compra_id' => $requerimiento->id,
                    'item_id' => $detalle->servicio_id,
                    'tipo_item' => 'servicio', 
                    'cantidad' => $detalle->cantidad,
                    'descripcion' => "Servicio: $nombreServicio",
                    'cotizacion_detalle_id' => $detalle->id,
                ]);
            }
        }
        
        // Registrar acción en historial de cotización
        HistorialCotizacion::create([
            'cotizacion_id' => $cotizacion->id,
            'estado_anterior_id' => $cotizacion->estado_id,
            'estado_nuevo_id' => $cotizacion->estado_id,
            'user_id' => Auth::id(),
            'comentario' => "Se generó requerimiento de compra #{$requerimiento->id}",
        ]);
        
        // Crear seguimiento automático
        SeguimientoCotizacion::create([
            'cotizacion_id' => $cotizacion->id,
            'user_id' => Auth::id(),
            'tipo' => 'nota',
            'contenido' => "Se generó requerimiento de compra #{$requerimiento->id} " . 
                          ($request->comentario ? ": {$request->comentario}" : ""),
            'fecha_seguimiento' => now(),
        ]);
        
        DB::commit();
        
        return redirect()->back()->with('success', 'Requerimiento de compra generado exitosamente');
        
    } catch (\Exception $e) {
        DB::rollBack();
        Log::error('Error al generar requerimiento de compra: ' . $e->getMessage(), [
            'cotizacion_id' => $cotizacion->id,
            'file' => $e->getFile(),
            'line' => $e->getLine(),
        ]);
        
        return redirect()->back()->with('error', 'Error al generar el requerimiento de compra: ' . $e->getMessage());
    }
}
}