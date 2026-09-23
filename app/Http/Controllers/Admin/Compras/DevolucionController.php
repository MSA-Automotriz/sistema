<?php

namespace App\Http\Controllers\Admin\Compras;

use App\Http\Controllers\Controller;
use App\Models\ValeDevolucion;
use App\Models\DetalleValeDevolucion;
use App\Models\Proveedor;
use App\Models\Parte;
use App\Models\Vehiculo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class DevolucionController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $devoluciones = ValeDevolucion::with(['proveedor', 'usuario'])
            ->orderBy('created_at', 'desc')
            ->paginate(15);
        
        return view('admin.compras.documentos.devoluciones.index', compact('devoluciones'));
    }

    public function create()
    {
        $proveedores = Proveedor::orderBy('razon_social')->get();
        return view('admin.compras.documentos.devoluciones.create', compact('proveedores'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'proveedor_id' => 'required|exists:proveedores,id',
            'motivo' => 'required|string|max:255',
            'fecha' => 'required|date',
            'observaciones' => 'nullable|string',
            'productos' => 'required|array|min:1',
            'productos.*.id' => 'required|integer',
            'productos.*.tipo' => 'required|in:parte,vehiculo',
            'productos.*.cantidad' => 'required|numeric|min:0.01',
            'productos.*.precio' => 'required|numeric|min:0',
            'productos.*.motivo' => 'nullable|string'
        ]);

        DB::beginTransaction();
        try {
            // Crear el vale de devolución
            $vale = ValeDevolucion::create([
                'numero' => ValeDevolucion::generarNumero(),
                'fecha' => $request->fecha,
                'proveedor_id' => $request->proveedor_id,
                'motivo' => $request->motivo,
                'observaciones' => $request->observaciones,
                'estado' => 'pendiente',
                'usuario_id' => Auth::id()
            ]);

            // Crear los detalles
            foreach ($request->productos as $producto) {
                $item = null;
                if ($producto['tipo'] === 'parte') {
                    $item = Parte::find($producto['id']);
                } else {
                    $item = Vehiculo::find($producto['id']);
                }

                if ($item) {
                    DetalleValeDevolucion::create([
                        'vale_devolucion_id' => $vale->id,
                        'producto_id' => $producto['id'],
                        'tipo_producto' => $producto['tipo'],
                        'codigo_producto' => $item->codigo ?? 'N/A',
                        'nombre_producto' => $item->nombre ?? 'N/A',
                        'cantidad' => $producto['cantidad'],
                        'precio_unitario' => $producto['precio'],
                        'motivo_detalle' => $producto['motivo'] ?? null
                    ]);
                }
            }

            DB::commit();
            return redirect()->route('admin.devoluciones.show', $vale->id)
                ->with('success', 'Vale de devolución creado correctamente.');

        } catch (\Exception $e) {
            DB::rollback();
            return back()->withInput()
                ->with('error', 'Error al crear el vale de devolución: ' . $e->getMessage());
        }
    }

    public function show($id)
    {
        $devolucion = ValeDevolucion::with(['proveedor', 'usuario', 'detalles', 'aprobadoPor'])
            ->findOrFail($id);
        
        return view('admin.compras.documentos.devoluciones.show', compact('devolucion'));
    }

    public function edit($id)
    {
        $devolucion = ValeDevolucion::with(['proveedor', 'detalles'])
            ->findOrFail($id);
        
        // Solo permitir editar si está en estado pendiente
        if ($devolucion->estado !== 'pendiente') {
            return redirect()->route('admin.devoluciones.show', $id)
                ->with('error', 'Solo se pueden editar vales de devolución en estado pendiente.');
        }
        
        $proveedores = Proveedor::orderBy('razon_social')->get();
        
        return view('admin.compras.documentos.devoluciones.edit', compact('devolucion', 'proveedores'));
    }

    public function update(Request $request, $id)
    {
        $devolucion = ValeDevolucion::findOrFail($id);
        
        // Solo permitir actualizar si está en estado pendiente
        if ($devolucion->estado !== 'pendiente') {
            return redirect()->route('admin.devoluciones.show', $id)
                ->with('error', 'Solo se pueden editar vales de devolución en estado pendiente.');
        }
        
        $request->validate([
            'proveedor_id' => 'required|exists:proveedores,id',
            'motivo' => 'required|string|max:255',
            'fecha' => 'required|date',
            'observaciones' => 'nullable|string',
            'productos' => 'required|array|min:1',
            'productos.*.id' => 'required|integer',
            'productos.*.tipo' => 'required|in:parte,vehiculo',
            'productos.*.cantidad' => 'required|numeric|min:0.01',
            'productos.*.precio' => 'required|numeric|min:0',
            'productos.*.motivo' => 'nullable|string'
        ]);

        DB::beginTransaction();
        try {
            // Actualizar el vale de devolución
            $devolucion->update([
                'fecha' => $request->fecha,
                'proveedor_id' => $request->proveedor_id,
                'motivo' => $request->motivo,
                'observaciones' => $request->observaciones
            ]);

            // Eliminar detalles existentes
            $devolucion->detalles()->delete();

            // Crear los nuevos detalles
            foreach ($request->productos as $producto) {
                $item = null;
                if ($producto['tipo'] === 'parte') {
                    $item = Parte::find($producto['id']);
                } else {
                    $item = Vehiculo::find($producto['id']);
                }

                if ($item) {
                    DetalleValeDevolucion::create([
                        'vale_devolucion_id' => $devolucion->id,
                        'producto_id' => $producto['id'],
                        'tipo_producto' => $producto['tipo'],
                        'codigo_producto' => $item->codigo ?? 'N/A',
                        'nombre_producto' => $item->nombre ?? 'N/A',
                        'cantidad' => $producto['cantidad'],
                        'precio_unitario' => $producto['precio'],
                        'motivo_detalle' => $producto['motivo'] ?? null
                    ]);
                }
            }

            DB::commit();
            return redirect()->route('admin.devoluciones.show', $devolucion->id)
                ->with('success', 'Vale de devolución actualizado correctamente.');

        } catch (\Exception $e) {
            DB::rollback();
            return back()->withInput()
                ->with('error', 'Error al actualizar el vale de devolución: ' . $e->getMessage());
        }
    }

    public function destroy($id)
    {
        $devolucion = ValeDevolucion::findOrFail($id);

        // Solo permitir eliminar si está en estado pendiente
        if ($devolucion->estado !== 'pendiente') {
            return redirect()->route('admin.devoluciones.index')
                ->with('error', 'Solo se pueden eliminar vales de devolución en estado pendiente.');
        }

        try {
            $numero = $devolucion->numero;
            $devolucion->delete();

            return redirect()->route('admin.devoluciones.index')
                ->with('success', "Vale de devolución {$numero} eliminado correctamente.");
        } catch (\Exception $e) {
            return redirect()->route('admin.devoluciones.index')
                ->with('error', 'Error al eliminar el vale de devolución: ' . $e->getMessage());
        }
    }

    public function buscarProductos(Request $request)
    {
        $search = $request->input('search', '');
        $tipo = $request->input('tipo', '');

        $resultados = [];

        // Buscar partes
        if ($tipo === '' || $tipo === 'parte') {
            $partes = Parte::where(function($query) use ($search) {
                $query->where('codigo', 'LIKE', "%{$search}%")
                      ->orWhere('nombre', 'LIKE', "%{$search}%");

                // Buscar en campos opcionales si existen
                if (\Schema::hasColumn('partes', 'marca')) {
                    $query->orWhere('marca', 'LIKE', "%{$search}%");
                }
                if (\Schema::hasColumn('partes', 'codigo_oem')) {
                    $query->orWhere('codigo_oem', 'LIKE', "%{$search}%");
                }
            })
            ->with('inventarios')
            ->limit(10)
            ->get()
            ->map(function($parte) {
                $stockTotal = $parte->inventarios->sum('stock_disponible');
                return [
                    'id' => $parte->id,
                    'tipo' => 'parte',
                    'codigo' => $parte->codigo ?? 'SIN-CODIGO',
                    'nombre' => $parte->nombre,
                    'descripcion' => $parte->marca ? "Marca: {$parte->marca}" : '',
                    'stock' => $stockTotal,
                    'precio' => $parte->precio_venta ?? $parte->precio_compra ?? 0
                ];
            })
            ->filter(function($parte) {
                return $parte['stock'] > 0;
            })
            ->values();

            $resultados = array_merge($resultados, $partes->toArray());
        }

        // Buscar vehículos
        if ($tipo === '' || $tipo === 'vehiculo') {
            $vehiculos = Vehiculo::where(function($query) use ($search) {
                $query->where('placa', 'LIKE', "%{$search}%")
                      ->orWhere('marca', 'LIKE', "%{$search}%")
                      ->orWhere('modelo', 'LIKE', "%{$search}%");

                if (\Schema::hasColumn('vehiculos', 'codigo')) {
                    $query->orWhere('codigo', 'LIKE', "%{$search}%");
                }
            })
            ->limit(10)
            ->get()
            ->map(function($vehiculo) {
                return [
                    'id' => $vehiculo->id,
                    'tipo' => 'vehiculo',
                    'codigo' => $vehiculo->placa,
                    'nombre' => "{$vehiculo->marca} {$vehiculo->modelo}",
                    'descripcion' => "Placa: {$vehiculo->placa}",
                    'stock' => 1,
                    'precio' => $vehiculo->precio_venta ?? 0
                ];
            });

            $resultados = array_merge($resultados, $vehiculos->toArray());
        }

        return response()->json($resultados);
    }
}