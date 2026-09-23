<?php

namespace App\Http\Controllers\Admin\Inventario;

use App\Http\Controllers\Controller;
use App\Models\DevolucionProveedor;
use App\Models\DetalleDevolucionProveedor;
use App\Models\Proveedor;
use App\Models\Parte;
use App\Models\Almacen;
use App\Models\Inventario;
use App\Models\Movimiento;
use App\Models\TipoMovimiento;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class DevolucionProveedorController extends Controller
{
    public function index()
    {
        $devoluciones = DevolucionProveedor::with(['proveedor', 'usuario', 'almacen'])
            ->orderBy('created_at', 'desc')
            ->paginate(15);
            
        return view('admin.inventario.devolucion-proveedor.index', compact('devoluciones'));
    }
    
    public function create()
    {
        $proveedores = Proveedor::orderBy('razon_social')->get();
        $almacenes = Almacen::orderBy('nombre')->get();
        
        return view('admin.inventario.devolucion-proveedor.create', compact('proveedores', 'almacenes'));
    }
    
    public function store(Request $request)
    {
        $request->validate([
            'proveedor_id' => 'required|exists:proveedores,id',
            'motivo' => 'required|string|max:255',
            'fecha_emision' => 'required|date',
            'observaciones' => 'nullable|string',
            'almacen_id' => 'required|exists:almacenes,id',
            'items' => 'required|array|min:1',
            'items.*.id' => 'required|integer',
            'items.*.tipo' => 'required|in:parte,vehiculo',
            'items.*.cantidad' => 'required|numeric|min:0.01',
            'items.*.motivo_detalle' => 'nullable|string',
        ]);
        
        DB::beginTransaction();
        
        try {
            // Generar código manualmente ya que parece que el método estático no está funcionando
            $anio = date('Y');
            $mes = date('m');
            $ultimaDevolucion = DevolucionProveedor::whereYear('created_at', $anio)
                ->whereMonth('created_at', $mes)
                ->orderBy('id', 'desc')
                ->first();
            
            $numero = $ultimaDevolucion ? intval(substr($ultimaDevolucion->codigo, -6)) + 1 : 1;
            $codigo = 'DEV-' . $anio . $mes . str_pad($numero, 6, '0', STR_PAD_LEFT);
            
            // Crear la devolución con el código generado manualmente
            $devolucion = DevolucionProveedor::create([
                'codigo' => $codigo,
                'proveedor_id' => $request->proveedor_id,
                'motivo' => $request->motivo,
                'fecha_emision' => $request->fecha_emision,
                'observaciones' => $request->observaciones,
                'estado' => 'PENDIENTE',
                'usuario_id' => Auth::id(),
                'almacen_id' => $request->almacen_id
            ]);
            
            // Crear los detalles
            foreach ($request->items as $item) {
                $detalle = DetalleDevolucionProveedor::create([
                    'devolucion_proveedor_id' => $devolucion->id,
                    'item_id' => $item['id'],
                    'tipo_item' => $item['tipo'],
                    'cantidad' => $item['cantidad'],
                    'motivo_detalle' => $item['motivo_detalle'] ?? null
                ]);
                
                // Si es una parte, actualizar inventario
                if ($item['tipo'] === 'parte') {
                    $this->actualizarInventario($item['id'], $request->almacen_id, $item['cantidad'], $devolucion);
                }
            }
            
            DB::commit();
            
            return redirect()->route('admin.inventario.devoluciones.index')
                ->with('success', 'Devolución a proveedor registrada correctamente.');
                
        } catch (\Exception $e) {
            DB::rollback();
            return back()->withInput()->with('error', 'Error al registrar la devolución: ' . $e->getMessage());
        }
    }
    
    public function show($id)
    {
        $devolucion = DevolucionProveedor::with(['proveedor', 'usuario', 'almacen', 'detalles.item'])
            ->findOrFail($id);
            
        return view('admin.inventario.devolucion-proveedor.show', compact('devolucion'));
    }
    
    public function edit($id)
    {
        $devolucion = DevolucionProveedor::with(['proveedor', 'usuario', 'almacen', 'detalles.item'])
            ->findOrFail($id);
            
        // Solo se puede editar si está pendiente
        if ($devolucion->estado !== 'PENDIENTE') {
            return redirect()->route('admin.inventario.devoluciones.show', $devolucion->id)
                ->with('error', 'No se puede editar una devolución que ya ha sido procesada.');
        }
        
        $proveedores = Proveedor::orderBy('razon_social')->get();
        $almacenes = Almacen::orderBy('nombre')->get();
        
        return view('admin.inventario.devolucion-proveedor.edit', compact('devolucion', 'proveedores', 'almacenes'));
    }
    
    public function update(Request $request, $id)
    {
        $request->validate([
            'proveedor_id' => 'required|exists:proveedores,id',
            'motivo' => 'required|string|max:255',
            'fecha_emision' => 'required|date',
            'observaciones' => 'nullable|string',
            'almacen_id' => 'required|exists:almacenes,id',
            'items' => 'required|array|min:1',
            'items.*.id' => 'required|integer',
            'items.*.tipo' => 'required|in:parte,vehiculo',
            'items.*.cantidad' => 'required|numeric|min:0.01',
            'items.*.motivo_detalle' => 'nullable|string',
        ]);
        
        $devolucion = DevolucionProveedor::findOrFail($id);
        
        // Solo se puede editar si está pendiente
        if ($devolucion->estado !== 'PENDIENTE') {
            return redirect()->route('admin.inventario.devoluciones.show', $devolucion->id)
                ->with('error', 'No se puede editar una devolución que ya ha sido procesada.');
        }
        
        DB::beginTransaction();
        
        try {
            // Actualizar la devolución
            $devolucion->update([
                'proveedor_id' => $request->proveedor_id,
                'motivo' => $request->motivo,
                'fecha_emision' => $request->fecha_emision,
                'observaciones' => $request->observaciones,
                'almacen_id' => $request->almacen_id
            ]);
            
            // Primero revertir el inventario de los items actuales
            foreach ($devolucion->detalles as $detalle) {
                if ($detalle->tipo_item === 'parte') {
                    $this->revertirInventario($detalle->item_id, $devolucion->almacen_id, $detalle->cantidad);
                }
            }
            
            // Eliminar detalles anteriores
            $devolucion->detalles()->delete();
            
            // Crear los nuevos detalles
            foreach ($request->items as $item) {
                $detalle = DetalleDevolucionProveedor::create([
                    'devolucion_proveedor_id' => $devolucion->id,
                    'item_id' => $item['id'],
                    'tipo_item' => $item['tipo'],
                    'cantidad' => $item['cantidad'],
                    'motivo_detalle' => $item['motivo_detalle'] ?? null
                ]);
                
                // Si es una parte, actualizar inventario
                if ($item['tipo'] === 'parte') {
                    $this->actualizarInventario($item['id'], $request->almacen_id, $item['cantidad'], $devolucion);
                }
            }
            
            DB::commit();
            
            return redirect()->route('admin.inventario.devoluciones.show', $devolucion->id)
                ->with('success', 'Devolución a proveedor actualizada correctamente.');
                
        } catch (\Exception $e) {
            DB::rollback();
            return back()->withInput()->with('error', 'Error al actualizar la devolución: ' . $e->getMessage());
        }
    }
    
    public function destroy($id)
    {
        $devolucion = DevolucionProveedor::findOrFail($id);
        
        // Solo se puede eliminar si está pendiente
        if ($devolucion->estado !== 'PENDIENTE') {
            return redirect()->route('admin.inventario.devoluciones.show', $devolucion->id)
                ->with('error', 'No se puede eliminar una devolución que ya ha sido procesada.');
        }
        
        DB::beginTransaction();
        
        try {
            // Revertir el inventario
            foreach ($devolucion->detalles as $detalle) {
                if ($detalle->tipo_item === 'parte') {
                    $this->revertirInventario($detalle->item_id, $devolucion->almacen_id, $detalle->cantidad);
                }
            }
            
            // Eliminar detalles
            $devolucion->detalles()->delete();
            
            // Eliminar la devolución
            $devolucion->delete();
            
            DB::commit();
            
            return redirect()->route('admin.inventario.devoluciones.index')
                ->with('success', 'Devolución a proveedor eliminada correctamente.');
                
        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'Error al eliminar la devolución: ' . $e->getMessage());
        }
    }
    
    public function confirmar($id)
    {
        $devolucion = DevolucionProveedor::findOrFail($id);
        
        if ($devolucion->estado !== 'PENDIENTE') {
            return redirect()->route('admin.inventario.devoluciones.show', $devolucion->id)
                ->with('error', 'Esta devolución ya ha sido procesada.');
        }
        
        $devolucion->estado = 'PROCESADA';
        $devolucion->save();
        
        return redirect()->route('admin.inventario.devoluciones.show', $devolucion->id)
            ->with('success', 'Devolución a proveedor confirmada correctamente.');
    }
    
    // Método para buscar items (partes)
    public function buscarItems(Request $request)
    {
        $term = $request->input('q');
        
        $partes = Parte::where(function($query) use ($term) {
                $query->where('nombre', 'like', "%{$term}%")
                    ->orWhere('codigo', 'like', "%{$term}%");
            })
            ->with(['unidad', 'proveedor'])
            ->take(20)
            ->get()
            ->map(function($parte) {
                return [
                    'id' => $parte->id,
                    'text' => "{$parte->codigo} - {$parte->nombre}",
                    'tipo' => 'parte',
                    'codigo' => $parte->codigo,
                    'nombre' => $parte->nombre,
                    'unidad' => $parte->unidad->nombre ?? 'N/A',
                    'proveedor' => $parte->proveedor->nombre_completo ?? 'N/A'
                ];
            });
        
        return response()->json([
            'results' => $partes
        ]);
    }
    
    // Método para actualizar el inventario (disminuir stock)
    private function actualizarInventario($parteId, $almacenId, $cantidad, $devolucion)
    {
        // Buscar o crear el inventario
        $inventario = Inventario::firstOrCreate(
            ['parte_id' => $parteId, 'almacen_id' => $almacenId],
            [
                'stock_disponible' => 0,
                'stock_reservado' => 0,
                'stock_minimo' => 0,
                'stock_maximo' => 0
            ]
        );
        
        // Verificar stock suficiente
        if ($inventario->stock_disponible < $cantidad) {
            throw new \Exception("No hay suficiente stock disponible para la parte ID: {$parteId}");
        }
        
        // Registrar movimiento
        $tipoMovimiento = TipoMovimiento::where('nombre', 'Devolución a Proveedor')->first();
        if (!$tipoMovimiento) {
            $tipoMovimiento = TipoMovimiento::create([
                'nombre' => 'Devolución a Proveedor',
                'afecta_stock' => -1, // Disminuye el stock
            ]);
        }
        
        // Actualizar stock
        $stockAnterior = $inventario->stock_disponible;
        $inventario->stock_disponible -= $cantidad;
        $inventario->save();
        
        // Registrar movimiento
        Movimiento::create([
            'tipo_movimiento_id' => $tipoMovimiento->id,
            'parte_id' => $parteId,
            'almacen_id' => $almacenId,
            'cantidad' => $cantidad,
            'stock_anterior' => $stockAnterior,
            'stock_resultante' => $inventario->stock_disponible,
            'documento_tipo' => 'devolucion_proveedor',
            'documento_id' => $devolucion->id,
            'documento_referencia' => $devolucion->codigo,
            'fecha_movimiento' => now(),
            'usuario_id' => Auth::id(),
            'observaciones' => "Devolución al proveedor: {$devolucion->proveedor->nombre_completo}"
        ]);
    }
    
    // Método para revertir el inventario (aumentar stock)
    private function revertirInventario($parteId, $almacenId, $cantidad)
    {
        $inventario = Inventario::where('parte_id', $parteId)
            ->where('almacen_id', $almacenId)
            ->first();
            
        if ($inventario) {
            $inventario->stock_disponible += $cantidad;
            $inventario->save();
            
            // Eliminar el movimiento (opcional)
            // Movimiento::where('documento_tipo', 'devolucion_proveedor')
            //     ->where('documento_id', $devolucionId)
            //     ->where('parte_id', $parteId)
            //     ->delete();
        }
    }
}