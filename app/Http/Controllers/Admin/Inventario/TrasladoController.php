<?php
namespace App\Http\Controllers\Admin\Inventario;

use App\Http\Controllers\Controller;
use App\Models\Traslado;
use App\Models\TrasladoItem;
use App\Models\Almacen;
use App\Models\Parte;
use App\Models\Vehiculo;
use App\Models\Inventario;
use App\Models\Kardex;
use App\Models\Movimiento;
use App\Models\TipoMovimiento;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class TrasladoController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $traslados = Traslado::with(['almacenOrigen', 'almacenDestino', 'usuario'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);
            
        return view('admin.inventario.traslados.index', compact('traslados'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $almacenes = Almacen::all();
        $partes = Parte::all();
        $vehiculos = Vehiculo::with(['marca', 'modelo', 'version', 'anioModelo'])->get();
        
        return view('admin.inventario.traslados.create', compact('almacenes', 'partes', 'vehiculos'));
    }

    /**
     * Get stock for a specific part or vehicle in a specific warehouse.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function getStock(Request $request)
    {
        $almacenId = $request->input('almacen_id');
        $tipo = $request->input('tipo_item');
        $itemId = $request->input('item_id');
        
        if ($tipo === 'parte') {
            $stock = DB::table('inventarios')
                ->where('almacen_id', $almacenId)
                ->where('parte_id', $itemId)
                ->value('stock_disponible') ?? 0;
        } else {
            $stock = DB::table('inventarios')
                ->where('almacen_id', $almacenId)
                ->where('vehiculo_id', $itemId)
                ->value('stock_disponible') ?? 0;
        }
        
        return response()->json(['stock' => (float)$stock]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'almacen_origen_id' => 'required|exists:almacenes,id',
            'almacen_destino_id' => 'required|exists:almacenes,id|different:almacen_origen_id',
            'motivo' => 'required|string',
            'tipo_item' => 'required|in:parte,vehiculo',
            'cantidad' => 'required|numeric|min:0.01',
        ]);
        
        if ($request->tipo_item === 'parte') {
            $request->validate([
                'parte_id' => 'required|exists:partes,id',
            ]);
            $itemId = $request->parte_id;
            $columnName = 'parte_id';
            $parte = Parte::find($itemId);
            $costoUnitario = $parte->precio_compra ?? 0;
        } else {
            $request->validate([
                'vehiculo_id' => 'required|exists:catalogos,id',
            ]);
            $itemId = $request->vehiculo_id;
            $columnName = 'vehiculo_id';
            $costoUnitario = 0;
        }
        
        // Verificar stock disponible en almacén origen
        $invOrigen = Inventario::where('almacen_id', $request->almacen_origen_id)
            ->where($columnName, $itemId)
            ->first();
            
        $stockDisponible = $invOrigen ? (float)$invOrigen->stock_disponible : 0;
            
        if ($stockDisponible < $request->cantidad) {
            return redirect()->back()
                ->withInput()
                ->withErrors(['cantidad' => 'La cantidad a trasladar no puede ser mayor al stock disponible (' . $stockDisponible . ').']);
        }
        
        DB::beginTransaction();
        
        try {
            // Crear el traslado
            $traslado = Traslado::create([
                'almacen_origen_id' => $request->almacen_origen_id,
                'almacen_destino_id' => $request->almacen_destino_id,
                'motivo' => $request->motivo,
                'fecha_traslado' => now(),
                'estado' => 'pendiente',
                'usuario_id' => Auth::id()
            ]);
            
            // Crear el item del traslado
            TrasladoItem::create([
                'traslado_id' => $traslado->id,
                'parte_id' => $request->tipo_item === 'parte' ? $itemId : null,
                'vehiculo_id' => $request->tipo_item === 'vehiculo' ? $itemId : null,
                'tipo_item' => $request->tipo_item,
                'cantidad' => $request->cantidad
            ]);
            
            // Disminuir stock en almacén origen
            $stockAntOrigen = $invOrigen->stock_disponible;
            $invOrigen->decrement('stock_disponible', $request->cantidad);
            $stockActOrigen = $invOrigen->fresh()->stock_disponible;
                
            // Aumentar stock en almacén destino
            $invDestino = Inventario::firstOrCreate(
                [
                    'almacen_id' => $request->almacen_destino_id,
                    $columnName => $itemId
                ],
                [
                    'stock_disponible' => 0,
                    'stock_reservado' => 0
                ]
            );
            $stockAntDestino = $invDestino->stock_disponible;
            $invDestino->increment('stock_disponible', $request->cantidad);
            $stockActDestino = $invDestino->fresh()->stock_disponible;
            
            // Registrar asiento contable en Kardex para salida origen
            Kardex::registrarTransferenciaSalida([
                'parte_id' => $request->tipo_item === 'parte' ? $itemId : null,
                'vehiculo_id' => $request->tipo_item === 'vehiculo' ? $itemId : null,
                'almacen_id' => $request->almacen_origen_id,
                'cantidad' => $request->cantidad,
                'stock_anterior' => $stockAntOrigen,
                'stock_actual' => $stockActOrigen,
                'costo_unitario' => $costoUnitario,
                'numero_documento' => 'TR-' . str_pad($traslado->id, 6, '0', STR_PAD_LEFT),
                'fecha_movimiento' => now(),
                'usuario_id' => Auth::id(),
                'referencia_id' => $traslado->id,
                'referencia_tipo' => 'App\Models\Traslado',
                'observaciones' => "Traslado a almacén destino: {$request->motivo}"
            ]);

            // Registrar asiento contable en Kardex para entrada destino
            Kardex::registrarTransferenciaEntrada([
                'parte_id' => $request->tipo_item === 'parte' ? $itemId : null,
                'vehiculo_id' => $request->tipo_item === 'vehiculo' ? $itemId : null,
                'almacen_id' => $request->almacen_destino_id,
                'cantidad' => $request->cantidad,
                'stock_anterior' => $stockAntDestino,
                'stock_actual' => $stockActDestino,
                'costo_unitario' => $costoUnitario,
                'numero_documento' => 'TR-' . str_pad($traslado->id, 6, '0', STR_PAD_LEFT),
                'fecha_movimiento' => now(),
                'usuario_id' => Auth::id(),
                'referencia_id' => $traslado->id,
                'referencia_tipo' => 'App\Models\Traslado',
                'observaciones' => "Recepción por traslado desde almacén origen: {$request->motivo}"
            ]);
            
            DB::commit();
            
            return redirect()->route('admin.inventario.traslados.index')
                ->with('success', 'Traslado realizado y registrado en Kardex con éxito.');
                
        } catch (\Exception $e) {
            DB::rollBack();
            
            return redirect()->back()
                ->withInput()
                ->withErrors(['error' => 'Error al realizar el traslado: ' . $e->getMessage()]);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Traslado  $traslado
     * @return \Illuminate\Http\Response
     */
    public function show(Traslado $traslado)
    {
        $traslado->load(['almacenOrigen', 'almacenDestino', 'usuario', 'items.parte', 'items.vehiculo']);
        
        return view('admin.inventario.traslados.show', compact('traslado'));
    }

    /**
     * Update the status of a transfer.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Traslado  $traslado
     * @return \Illuminate\Http\Response
     */
    public function cambiarEstado(Request $request, Traslado $traslado)
    {
        $request->validate([
            'estado' => 'required|in:completado,cancelado',
        ]);
        
        DB::beginTransaction();
        
        try {
            if ($request->estado == 'cancelado' && $traslado->estado == 'pendiente') {
                // Revertir el traslado
                foreach ($traslado->items as $item) {
                    $columnName = $item->tipo_item === 'parte' ? 'parte_id' : 'vehiculo_id';
                    $itemId = $item->tipo_item === 'parte' ? $item->parte_id : $item->vehiculo_id;
                    
                    // Devolver stock al almacén origen
                    Inventario::where('almacen_id', $traslado->almacen_origen_id)
                        ->where($columnName, $itemId)
                        ->increment('stock_disponible', $item->cantidad);
                        
                    // Reducir stock del almacén destino
                    Inventario::where('almacen_id', $traslado->almacen_destino_id)
                        ->where($columnName, $itemId)
                        ->decrement('stock_disponible', $item->cantidad);
                }
            }
            
            $traslado->estado = $request->estado;
            $traslado->save();
            
            DB::commit();
            
            return redirect()->route('admin.inventario.traslados.index')
                ->with('success', 'Estado del traslado actualizado correctamente.');
                
        } catch (\Exception $e) {
            DB::rollBack();
            
            return redirect()->back()
                ->withErrors(['error' => 'Error al actualizar el estado del traslado: ' . $e->getMessage()]);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Traslado  $traslado
     * @return \Illuminate\Http\Response
     */
    public function destroy(Traslado $traslado)
    {
        if ($traslado->estado != 'pendiente') {
            return redirect()->route('admin.inventario.traslados.index')
                ->withErrors(['error' => 'No se puede eliminar un traslado que ya ha sido completado o cancelado.']);
        }
        
        DB::beginTransaction();
        
        try {
            // Revertir el inventario antes de eliminar
            foreach ($traslado->items as $item) {
                $columnName = $item->tipo_item === 'parte' ? 'parte_id' : 'vehiculo_id';
                $itemId = $item->tipo_item === 'parte' ? $item->parte_id : $item->vehiculo_id;
                
                // Devolver stock al almacén origen
                Inventario::where('almacen_id', $traslado->almacen_origen_id)
                    ->where($columnName, $itemId)
                    ->increment('stock_disponible', $item->cantidad);
                    
                // Reducir stock del almacén destino
                Inventario::where('almacen_id', $traslado->almacen_destino_id)
                    ->where($columnName, $itemId)
                    ->decrement('stock_disponible', $item->cantidad);
            }
            
            // Eliminar el traslado (los items se eliminarán en cascada)
            $traslado->delete();
            
            DB::commit();
            
            return redirect()->route('admin.inventario.traslados.index')
                ->with('success', 'Traslado eliminado correctamente.');
                
        } catch (\Exception $e) {
            DB::rollBack();
            
            return redirect()->route('admin.inventario.traslados.index')
                ->withErrors(['error' => 'Error al eliminar el traslado: ' . $e->getMessage()]);
        }
    }
}