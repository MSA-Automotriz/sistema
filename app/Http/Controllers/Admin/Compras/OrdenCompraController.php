<?php

namespace App\Http\Controllers\Admin\Compras;
use Illuminate\Support\Facades\Schema;

use App\Http\Controllers\Controller;
use App\Models\OrdenCompra;
use App\Models\DetalleOrdenCompra;
use App\Models\RequerimientoCompra;
use App\Models\Parte;
use App\Models\Vehiculo;
use App\Models\Almacen;
use App\Models\Proveedor;
use App\Models\Inventario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class OrdenCompraController extends Controller
{
    public function index(Request $request)
    {
        try {
            Log::info('Acceso a lista de órdenes de compra', [
                'user_id' => Auth::id(),
                'filtros' => $request->all()
            ]);

            $query = OrdenCompra::with('requerimiento', 'almacen', 'usuario', 'proveedor');
            
            if ($request->filled('estado')) {
                $query->where('estado', $request->estado);
            }
            
            if ($request->filled('proveedor_id')) {
                $query->where('proveedor_id', $request->proveedor_id);
            }
            
            $ordenes = $query->latest()->paginate(10);
            $proveedores = Proveedor::all();
            
            return view('admin.compras.ordenes.index', compact('ordenes', 'proveedores'));

        } catch (\Exception $e) {
            Log::error('Error al listar órdenes de compra', [
                'user_id' => Auth::id(),
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return back()->with('error', 'Error al cargar la lista de órdenes');
        }
    }

    
    public function create(Request $request)
    {
        $requerimiento_id = $request->query('requerimiento_id');
        
        if (!$requerimiento_id) {
            return redirect()->route('admin.compras.requerimientos.index')
                ->with('error', 'Debe seleccionar un requerimiento de compra para crear una orden.');
        }
        
        $requerimiento = RequerimientoCompra::with(['almacen', 'detalles.item'])->findOrFail($requerimiento_id);
        $proveedores = Proveedor::all();
        
        return view('admin.compras.ordenes.create', compact('requerimiento', 'proveedores'));
    }
    
    public function store(Request $request)
    {
        $request->validate([
            'requerimiento_id' => 'required|exists:requerimientos_compra,id',
            'proveedor_id' => 'required|exists:proveedores,id',
            'moneda' => 'required|string|max:10',
            'observaciones' => 'nullable|string',
            'detalles' => 'required|array',
            'detalles.*.item_id' => 'required|numeric',
            'detalles.*.tipo_item' => 'required|in:parte,vehiculo',
            'detalles.*.cantidad_en_compra' => 'required|numeric|min:0.01',
            'detalles.*.precio_compra' => 'required|numeric|min:0',
            'detalles.*.afecto_igv' => 'boolean',
        ]);

        DB::beginTransaction();
        try {
            $requerimiento = RequerimientoCompra::with('almacen')->findOrFail($request->requerimiento_id);
            
            // Generación de código
            $ultimaOrden = OrdenCompra::latest()->first();
            $numeroOrden = $ultimaOrden ? (int)substr($ultimaOrden->codigo, 3) + 1 : 1;
            $codigo = 'OC-' . $numeroOrden;

            $ordenData = [
                'codigo' => $codigo,
                'requerimiento_compra_id' => $requerimiento->id,
                'tipo' => $requerimiento->tipo,
                'estado' => 'en espera',
                'almacen_destino_id' => $requerimiento->almacen_id,
                'requerido_por' => Auth::id(),
                'proveedor_id' => $request->proveedor_id,
                'moneda' => $request->moneda,
                'observaciones' => $request->observaciones,
                'estado_recepcion' => 'pendiente', // Inicializar estado de recepción
            ];

            $ordenCompra = OrdenCompra::create($ordenData);
            Log::info('Orden de compra creada', [
                'user_id' => Auth::id(),
                'orden_id' => $ordenCompra->id,
                'data' => $ordenData
            ]);

            $total = 0;
            foreach ($request->detalles as $detalle) {
                $this->createDetalleOrden($ordenCompra->id, $detalle, $total);
            }

            $ordenCompra->update(['total' => $total]);
            DB::commit();

            Log::info('Orden de compra completada exitosamente', [
                'user_id' => Auth::id(),
                'orden_id' => $ordenCompra->id,
                'total' => $total
            ]);

            return redirect()->route('admin.compras.ordenes.show', $ordenCompra)
                ->with('success', 'Orden de compra creada exitosamente.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error al crear orden de compra', [
                'user_id' => Auth::id(),
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'input' => $request->all()
            ]);
            return back()->withInput()
                ->with('error', 'Error al crear la orden: ' . $e->getMessage());
        }
    }
    
    public function show($id)
    {
        $orden = OrdenCompra::with([
            'requerimiento',
            'almacen',
            'usuario',
            'aprobador',
            'proveedor',
            'detalles'
        ])->findOrFail($id);
        
        return view('admin.compras.ordenes.show', compact('orden'));
    }
    
    public function edit($id)
    {
        $orden = OrdenCompra::with([
            'requerimiento',
            'detalles'
        ])->findOrFail($id);
        
        if ($orden->estado !== 'en espera') {
            return redirect()->route('admin.compras.ordenes.show', $orden)
                ->with('error', 'No se puede editar una orden que no está en estado "En Espera".');
        }
        
        $proveedores = Proveedor::all();
        
        return view('admin.compras.ordenes.edit', compact('orden', 'proveedores'));
    }
    
    public function update(Request $request, $id)
    {
        $orden = OrdenCompra::findOrFail($id);
        
        if ($orden->estado !== 'en espera') {
            return redirect()->route('admin.compras.ordenes.show', $orden)
                ->with('error', 'No se puede editar una orden que no está en estado "En Espera".');
        }
        
        $request->validate([
            'proveedor_id' => 'required|exists:proveedores,id',
            'moneda' => 'required|string|max:10',
            'observaciones' => 'nullable|string',
            'detalles' => 'required|array',
            'detalles.*.item_id' => 'required|numeric',
            'detalles.*.tipo_item' => 'required|in:parte,vehiculo',
            'detalles.*.cantidad_en_compra' => 'required|numeric|min:0.01',
            'detalles.*.precio_compra' => 'required|numeric|min:0',
            'detalles.*.afecto_igv' => 'boolean',
        ]);
        
        DB::beginTransaction();
        try {
            $orden->update([
                'proveedor_id' => $request->proveedor_id,
                'moneda' => $request->moneda,
                'observaciones' => $request->observaciones,
            ]);
            
            $orden->detalles()->delete();
            $total = 0;
            
            foreach ($request->detalles as $detalle) {
                $cantidad = floatval($detalle['cantidad_en_compra']);
                $precio = floatval($detalle['precio_compra']);
                $descuento = isset($detalle['descuento']) ? floatval($detalle['descuento']) : 0;
                $subtotal = ($cantidad * $precio) - $descuento;
                $total += $subtotal;
                
                $codigo = '';
                $nombreProducto = '';
                
                if ($detalle['tipo_item'] === 'parte') {
                    $parte = Parte::find($detalle['item_id']);
                    $codigo = $parte->codigo;
                    $nombreProducto = $parte->nombre;
                } else if ($detalle['tipo_item'] === 'vehiculo') {
                    $vehiculo = Vehiculo::with(['marca', 'modelo'])->find($detalle['item_id']);
                    $codigo = "V{$vehiculo->id}";
                    $nombreProducto = implode(' ', array_filter([
                        $vehiculo->marca?->nombre ?? '',
                        $vehiculo->modelo?->nombre ?? '',
                        $vehiculo->version?->nombre ?? '',
                        $vehiculo->anioModelo?->anio ?? '',
                    ]));
                }
                
                DetalleOrdenCompra::create([
                    'orden_compra_id' => $orden->id,
                    'item_id' => $detalle['item_id'],
                    'tipo_item' => $detalle['tipo_item'],
                    'codigo' => $codigo,
                    'nombre_producto' => $nombreProducto,
                    'cantidad_requerida' => $detalle['cantidad_requerida'],
                    'cantidad_en_compra' => $cantidad,
                    'unidad' => $detalle['unidad'] ?? 'UND',
                    'precio_compra' => $precio,
                    'descuento' => $descuento,
                    'total' => $subtotal,
                    'afecto_igv' => isset($detalle['afecto_igv']) ? $detalle['afecto_igv'] : true,
                    'cantidad_recibida' => 0, // Inicializar en 0
                    'estado_recepcion' => 'pendiente', // Estado inicial
                ]);
            }
            
            $orden->update(['total' => $total]);
            DB::commit();
            
            return redirect()->route('admin.compras.ordenes.show', $orden)
                ->with('success', 'Orden de compra actualizada exitosamente.');
                
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()
                ->with('error', 'Error al actualizar la orden: ' . $e->getMessage());
        }
    }

    private function createDetalleOrden($ordenId, $detalle, &$total)
    {
        try {
            $cantidad = floatval($detalle['cantidad_en_compra']);
            $precio = floatval($detalle['precio_compra']);
            $descuento = $detalle['descuento'] ?? 0;
            $subtotal = ($cantidad * $precio) - $descuento;
            $total += $subtotal;

            $itemInfo = $this->getItemInfo($detalle);
            
            $detalleData = [
                'orden_compra_id' => $ordenId,
                'item_id' => $detalle['item_id'],
                'tipo_item' => $detalle['tipo_item'],
                'codigo' => $itemInfo['codigo'],
                'nombre_producto' => $itemInfo['nombre'],
                'cantidad_requerida' => $detalle['cantidad_requerida'],
                'cantidad_en_compra' => $cantidad,
                'unidad' => $detalle['unidad'] ?? 'UND',
                'precio_compra' => $precio,
                'descuento' => $descuento,
                'total' => $subtotal,
                'afecto_igv' => $detalle['afecto_igv'] ?? true,
                'cantidad_recibida' => 0, // Inicializar en 0
                'estado_recepcion' => 'pendiente', // Estado inicial
            ];

            DetalleOrdenCompra::create($detalleData);
            Log::debug('Detalle de orden creado', [
                'orden_id' => $ordenId,
                'detalle' => $detalleData
            ]);

        } catch (\Exception $e) {
            Log::error('Error al crear detalle de orden', [
                'orden_id' => $ordenId,
                'detalle' => $detalle,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    private function getItemInfo($detalle)
    {
        $codigo = '';
        $nombre = '';
        
        if ($detalle['tipo_item'] === 'parte') {
            $parte = Parte::findOrFail($detalle['item_id']);
            $codigo = $parte->codigo;
            $nombre = $parte->nombre;
        } else {
            $vehiculo = Vehiculo::with(['marca', 'modelo'])->findOrFail($detalle['item_id']);
            $codigo = "V{$vehiculo->id}";
            $nombre = implode(' ', array_filter([
                $vehiculo->marca?->nombre,
                $vehiculo->modelo?->nombre,
                $vehiculo->version?->nombre,
                $vehiculo->anioModelo?->anio,
            ]));
        }
        
        return ['codigo' => $codigo, 'nombre' => $nombre];
    }
    
    /**
     * CORECCIÓN PRINCIPAL: La aprobación NO debe actualizar inventario
     * Solo cambia el estado de la orden, el inventario se actualiza en la recepción
     */
    public function aprobar($id)
    {
        $orden = OrdenCompra::with(['detalles', 'almacen'])->findOrFail($id);
        
        if ($orden->estado !== 'en espera') {
            Log::warning('Intento de aprobar orden no disponible', [
                'user_id' => Auth::id(),
                'orden_id' => $id,
                'estado_actual' => $orden->estado
            ]);
            return redirect()->route('admin.compras.ordenes.show', $orden)
                ->with('error', 'La orden ya ha sido procesada.');
        }

        DB::beginTransaction();
        try {
            Log::info('Inicio de aprobación de orden', [
                'user_id' => Auth::id(),
                'orden_id' => $id,
                'detalles_count' => $orden->detalles->count()
            ]);

            // SOLO cambiar el estado de la orden - NO tocar inventario
            $orden->update([
                'estado' => 'aprobada',
                'aprobado_por' => Auth::id(),
                'fecha_aprobacion' => now(),
            ]);
            
            // Para vehículos, actualizar su estado a "en_orden_compra" (pendiente de recepción)
            foreach ($orden->detalles as $detalle) {
                if ($detalle->tipo_item === 'vehiculo') {
                    $vehiculo = Vehiculo::find($detalle->item_id);
                    if ($vehiculo && Schema::hasColumn('vehiculos', 'estado')) {
                        $vehiculo->update(['estado' => 'en_orden_compra']);
                    }
                    Log::info('Estado de vehículo actualizado a en_orden_compra', [
                        'vehiculo_id' => $detalle->item_id
                    ]);
                }
            }
            
            DB::commit();
            Log::info('Orden aprobada exitosamente (sin afectar inventario)', [
                'user_id' => Auth::id(),
                'orden_id' => $id
            ]);

            return redirect()->route('admin.compras.ordenes.show', $orden)
                ->with('success', 'Orden de compra aprobada exitosamente. El inventario se actualizará cuando se reciban los productos.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error al aprobar orden', [
                'user_id' => Auth::id(),
                'orden_id' => $id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return redirect()->route('admin.compras.ordenes.show', $orden)
                ->with('error', 'Error al aprobar la orden: ' . $e->getMessage());
        }
    }
    
    public function rechazar($id)
    {
        $orden = OrdenCompra::findOrFail($id);
        
        if ($orden->estado !== 'en espera') {
            return redirect()->route('admin.compras.ordenes.show', $orden)
                ->with('error', 'La orden ya ha sido procesada.');
        }
        
        $orden->update([
            'estado' => 'rechazada',
            'aprobado_por' => Auth::id(),
            'fecha_aprobacion' => now(),
        ]);
        
        return redirect()->route('admin.compras.ordenes.show', $orden)
            ->with('success', 'Orden de compra rechazada.');
    }
    
    /**
     * CORREGIDO: La reversión ya no necesita revertir inventario
     * porque la aprobación no lo modificaba
     */
    public function revertirAprobacion($id)
    {
        $orden = OrdenCompra::with(['detalles', 'almacen'])->findOrFail($id);
        
        if ($orden->estado !== 'aprobada') {
            return redirect()->route('admin.compras.ordenes.show', $orden)
                ->with('error', 'Solo se puede revertir una orden aprobada.');
        }

        // Verificar que no tenga recepciones
        $tieneRecepciones = $orden->detalles()->where('cantidad_recibida', '>', 0)->exists();
        if ($tieneRecepciones) {
            return redirect()->route('admin.compras.ordenes.show', $orden)
                ->with('error', 'No se puede revertir una orden que ya tiene recepciones registradas.');
        }
        
        DB::beginTransaction();
        try {
            // Revertir estado de vehículos si corresponde
            foreach ($orden->detalles as $detalle) {
                if ($detalle->tipo_item === 'vehiculo') {
                    $vehiculo = Vehiculo::find($detalle->item_id);
                    if ($vehiculo && Schema::hasColumn('vehiculos', 'estado')) {
                        $vehiculo->update(['estado' => 'disponible']); // o el estado que corresponda
                    }
                }
            }
            
            $orden->update([
                'estado' => 'en espera',
                'aprobado_por' => null,
                'fecha_aprobacion' => null,
            ]);
            
            DB::commit();
            
            return redirect()->route('admin.compras.ordenes.show', $orden)
                ->with('success', 'Aprobación de orden revertida exitosamente.');
                
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('admin.compras.ordenes.show', $orden)
                ->with('error', 'Ocurrió un error al revertir la aprobación: ' . $e->getMessage());
        }
    }
    
    public function destroy($id)
    {
        $orden = OrdenCompra::findOrFail($id);
        
        if ($orden->estado !== 'en espera') {
            Log::warning('Intento de eliminar orden no disponible', [
                'user_id' => Auth::id(),
                'orden_id' => $id,
                'estado_actual' => $orden->estado
            ]);
            return redirect()->route('admin.compras.ordenes.index')
                ->with('error', 'No se puede eliminar una orden que no está en estado "En Espera".');
        }

        DB::beginTransaction();
        try {
            Log::info('Inicio de eliminación de orden', [
                'user_id' => Auth::id(),
                'orden_id' => $id
            ]);

            $orden->detalles()->delete();
            $orden->delete();
            
            DB::commit();
            Log::info('Orden eliminada exitosamente', [
                'user_id' => Auth::id(),
                'orden_id' => $id
            ]);

            return redirect()->route('admin.compras.ordenes.index')
                ->with('success', 'Orden de compra eliminada exitosamente.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error al eliminar orden', [
                'user_id' => Auth::id(),
                'orden_id' => $id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return redirect()->route('admin.compras.ordenes.index')
                ->with('error', 'Error al eliminar la orden: ' . $e->getMessage());
        }
    }

    /**
     * MÉTODOS DE APOYO: Para manejar estados de vehículos en casos específicos
     * El inventario se maneja completamente en InventarioService
     */
    protected function actualizarEstadoVehiculo(Vehiculo $vehiculo, $almacenId)
    {
        if (Schema::hasColumn('vehiculos', 'estado')) {
            $vehiculo->update([
                'almacen_id' => $almacenId,
                'estado' => 'disponible',
            ]);
        } else {
            $vehiculo->update([
                'almacen_id' => $almacenId,
            ]);
        }
    }
    
    protected function revertirEstadoVehiculo(Vehiculo $vehiculo)
    {
        if (Schema::hasColumn('vehiculos', 'estado')) {
            $vehiculo->update([
                'almacen_id' => null,
                'estado' => 'en_orden_compra',
            ]);
        } else {
            $vehiculo->update([
                'almacen_id' => null,
            ]);
        }
    }
}