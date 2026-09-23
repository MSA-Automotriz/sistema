<?php

namespace App\Http\Controllers\Admin\Compras;

use App\Http\Controllers\Controller;
use App\Models\OrdenCompra;
use App\Models\RecepcionOrdenCompra;
use App\Models\DevolucionOrdenCompra;
use App\Services\InventarioService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class RecepcionController extends Controller
{
    protected $inventarioService;

    public function __construct(InventarioService $inventarioService)
    {
        $this->inventarioService = $inventarioService;
    }

    public function index()
    {
        // Obtener TODAS las órdenes para poder filtrarlas en las pestañas
        $ordenes = OrdenCompra::with(['proveedor', 'detalles'])->get();
        
        return view('admin.compras.documentos.recepcion.index', compact('ordenes'));
    }

    public function show(OrdenCompra $ordenCompra)
    {
        $orden = $ordenCompra->load(['proveedor', 'almacen', 'detalles.recepciones']);
        
        return view('admin.compras.documentos.recepcion.show', compact('orden'));
    }

    public function store(Request $request, OrdenCompra $ordenCompra)
    {
        $request->validate([
            'fecha_recepcion' => 'required|date',
            'recepciones' => 'required|array',
            'recepciones.*.cantidad_recibida' => 'required|integer|min:0',
            'tipo_recepcion' => 'required|in:normal,completa_con_faltantes'
        ]);

        DB::transaction(function () use ($request, $ordenCompra) {
            foreach ($request->recepciones as $detalleId => $recepcion) {
                if ($recepcion['cantidad_recibida'] > 0) {
                    // Crear registro de recepción
                    $recepcionRegistro = RecepcionOrdenCompra::create([
                        'detalle_orden_compra_id' => $detalleId,
                        'cantidad_recibida' => $recepcion['cantidad_recibida'],
                        'fecha_recepcion' => $request->fecha_recepcion,
                        'observaciones' => $recepcion['observaciones'] ?? null,
                        'recibido_por' => Auth::id(),
                    ]);

                    // PROCESAR INVENTARIO Y KARDEX
                    $this->inventarioService->procesarRecepcion($recepcionRegistro);

                    // Actualizar detalle de orden
                    $detalle = $ordenCompra->detalles()->find($detalleId);
                    $nuevaCantidadRecibida = ($detalle->cantidad_recibida ?? 0) + $recepcion['cantidad_recibida'];
                    
                    // Determinar estado según tipo de recepción
                    $estadoRecepcion = 'parcial';
                    if ($request->tipo_recepcion == 'completa_con_faltantes') {
                        $estadoRecepcion = 'completo_con_faltantes';
                    } elseif ($nuevaCantidadRecibida >= $detalle->cantidad_en_compra) {
                        $estadoRecepcion = 'completo';
                    }
                    
                    $detalle->update([
                        'cantidad_recibida' => $nuevaCantidadRecibida,
                        'estado_recepcion' => $estadoRecepcion
                    ]);
                }
            }

            // Si es recepción completa con faltantes, marcar todos los detalles restantes
            if ($request->tipo_recepcion == 'completa_con_faltantes') {
                foreach ($ordenCompra->detalles as $detalle) {
                    if (!isset($request->recepciones[$detalle->id]) || 
                        $request->recepciones[$detalle->id]['cantidad_recibida'] == 0) {
                        $detalle->update([
                            'estado_recepcion' => 'completo_con_faltantes',
                            'motivo_faltante' => $request->motivo_faltantes ?? 'Items no recibidos'
                        ]);
                    }
                }
                
                $ordenCompra->update([
                    'motivo_faltantes' => $request->motivo_faltantes ?? 'Recepción completada con faltantes',
                    'fecha_completado_faltantes' => $request->fecha_recepcion
                ]);
            }

            // Actualizar estado general de la orden
            $ordenCompra->actualizarEstadoRecepcion();
        });

        $mensaje = $request->tipo_recepcion == 'completa_con_faltantes' 
            ? 'Recepción completada con items faltantes. Inventario actualizado correctamente.'
            : 'Recepción registrada correctamente. Inventario y kardex actualizados.';

        return redirect()->route('admin.recepcion.index')
            ->with('success', $mensaje);
    }

    public function completarConFaltantes(Request $request, OrdenCompra $ordenCompra)
    {
        $request->validate([
            'motivo' => 'required|string|max:500',
            'fecha_recepcion' => 'required|date'
        ]);

        DB::transaction(function () use ($request, $ordenCompra) {
            // Marcar todos los detalles como completos con faltantes
            foreach ($ordenCompra->detalles as $detalle) {
                if ($detalle->estado_recepcion != 'completo') {
                    $detalle->update([
                        'estado_recepcion' => 'completo_con_faltantes',
                        'motivo_faltante' => $request->motivo
                    ]);
                }
            }

            $ordenCompra->update([
                'estado_recepcion' => 'completo_con_faltantes',
                'motivo_faltantes' => $request->motivo,
                'fecha_completado_faltantes' => $request->fecha_recepcion
            ]);
        });

        return redirect()->route('admin.recepcion.index')
            ->with('success', 'Orden marcada como completa con items faltantes');
    }

    public function devolver(Request $request, OrdenCompra $ordenCompra)
    {
        $request->validate([
            'detalle_id' => 'required|exists:detalle_orden_compras,id',
            'cantidad_devolver' => 'required|integer|min:1',
            'motivo' => 'required|string|max:500',
            'fecha_devolucion' => 'required|date'
        ]);

        DB::transaction(function () use ($request, $ordenCompra) {
            $detalle = $ordenCompra->detalles()->find($request->detalle_id);
            
            // Verificar que no se devuelva más de lo recibido
            if ($request->cantidad_devolver > $detalle->cantidad_recibida) {
                throw new \Exception('No se puede devolver más cantidad de la recibida');
            }

            // Crear registro de devolución
            $devolucion = DevolucionOrdenCompra::create([
                'detalle_orden_compra_id' => $request->detalle_id,
                'cantidad_devuelta' => $request->cantidad_devolver,
                'motivo' => $request->motivo,
                'fecha_devolucion' => $request->fecha_devolucion,
                'devuelto_por' => Auth::id(),
            ]);

            // PROCESAR DEVOLUCIÓN EN INVENTARIO Y KARDEX
            $this->inventarioService->procesarDevolucion($devolucion);

            // Actualizar cantidad recibida en el detalle
            $nuevaCantidadRecibida = $detalle->cantidad_recibida - $request->cantidad_devolver;
            $estadoRecepcion = $nuevaCantidadRecibida == 0 ? 'pendiente' : 
                             ($nuevaCantidadRecibida >= $detalle->cantidad_en_compra ? 'completo' : 'parcial');

            $detalle->update([
                'cantidad_recibida' => $nuevaCantidadRecibida,
                'estado_recepcion' => $estadoRecepcion
            ]);

            // Actualizar estado general de la orden
            $ordenCompra->actualizarEstadoRecepcion();
        });

        return redirect()->back()
            ->with('success', 'Devolución registrada correctamente. Inventario y kardex actualizados.');
    }

    public function detalle(OrdenCompra $ordenCompra)
    {
        $orden = $ordenCompra->load([
            'proveedor', 
            'almacen', 
            'detalles.recepciones.recibidoPor',
            'detalles.devoluciones.devueltoPor'
        ]);
        
        return view('admin.compras.documentos.recepcion.detalle', compact('orden'));
    }

    public function historial()
    {
        $recepciones = RecepcionOrdenCompra::with([
            'detalleOrdenCompra.ordenCompra.proveedor',
            'recibidoPor'
        ])->orderBy('fecha_recepcion', 'desc')->get();
        
        return view('admin.compras.documentos.recepcion.historial', compact('recepciones'));
    }
}