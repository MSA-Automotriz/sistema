<?php

namespace App\Http\Controllers\Admin\Compras;

use App\Http\Controllers\Controller;
use App\Models\RequerimientoCompra;
use App\Models\DetalleRequerimientoCompra;
use App\Models\EstadoRequerimiento;
use App\Models\Almacen;
use App\Models\Vehiculo;
use App\Models\Estado;
use App\Models\Parte;
use App\Models\Proveedor; 
use Illuminate\Http\Request;

class RequerimientoCompraController extends Controller
{
    // Método Index
    public function index(Request $request)
    {
        $almacenes = Almacen::with('allChildren')->whereNull('parent_id')->get();
        $query = RequerimientoCompra::with('almacen', 'detalles', 'proveedor'); // Agregar proveedor

        if ($request->filled('almacen_id')) {
            $query->where('almacen_id', $request->almacen_id);
        }

        $requerimientos = $query->latest()->paginate(10);

        return view('admin.compras.requerimientos.index', compact('requerimientos', 'almacenes'));
    }

    public function create()
    {
        $almacenes = Almacen::with('allChildren')->whereNull('parent_id')->get();
        return view('admin.compras.requerimientos.create', compact('almacenes'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'almacen_id' => 'required|exists:almacenes,id',
            'proveedor_id' => 'nullable|exists:proveedores,id', // Validación opcional del proveedor
            'detalles' => 'required|array',
            'detalles.*.item_id' => 'required|numeric',
            'detalles.*.tipo_item' => 'required|in:parte,vehiculo',
            'detalles.*.cantidad' => 'required|numeric|min:0.01',
        ]);

        // Estado inicial fijo (ID 1)
        $estadoInicialId = 1;
        
        // Generar código automático para el requerimiento
        $ultimoRequerimiento = RequerimientoCompra::latest('id')->first();
        $numeroRequerimiento = $ultimoRequerimiento ? $ultimoRequerimiento->id + 1 : 1;
        $codigo = 'REQ-' . str_pad($numeroRequerimiento, 6, '0', STR_PAD_LEFT);
        
        $requerimiento = RequerimientoCompra::create([
            'codigo' => $codigo,
            'tipo' => 'inventario',
            'almacen_id' => $request->almacen_id,
            'proveedor_id' => $request->proveedor_id, // Agregar proveedor_id
            'estado_id' => $estadoInicialId,
            'user_id' => auth()->id(),
            'fecha' => now(),
        ]);

        foreach ($request->detalles as $detalle) {
            DetalleRequerimientoCompra::create([
                'requerimiento_compra_id' => $requerimiento->id,
                'item_id' => $detalle['item_id'],
                'tipo_item' => $detalle['tipo_item'],
                'cantidad' => $detalle['cantidad'],
            ]);
        }

        return redirect()->route('admin.compras.requerimientos.index')
            ->with('success', 'Requerimiento de compra creado exitosamente.');
    }

    public function edit($id)
    {
        \Log::info('Editando requerimiento ID: ' . $id);
        $requerimiento = RequerimientoCompra::with('detalles.item', 'proveedor')->findOrFail($id);
        \Log::info('Requerimiento cargado: ' . $requerimiento->id);
        $almacenes = Almacen::with('allChildren')->whereNull('parent_id')->get();
        return view('admin.compras.requerimientos.edit', compact('requerimiento', 'almacenes'));
    }

    public function update(Request $request, $id)
    {
        $requerimiento = RequerimientoCompra::findOrFail($id);

        $request->validate([
            'almacen_id' => 'required|exists:almacenes,id',
            'proveedor_id' => 'nullable|exists:proveedores,id', // Validación opcional del proveedor
            'detalles' => 'required|array',
            'detalles.*.item_id' => 'required|numeric',
            'detalles.*.tipo_item' => 'required|in:parte,vehiculo',
            'detalles.*.cantidad' => 'required|numeric|min:0.01',
        ]);

        $requerimiento->update([
            'almacen_id' => $request->almacen_id,
            'proveedor_id' => $request->proveedor_id, // Actualizar proveedor_id
        ]);

        $requerimiento->detalles()->delete();
        foreach ($request->detalles as $detalle) {
            DetalleRequerimientoCompra::create([
                'requerimiento_compra_id' => $requerimiento->id,
                'item_id' => $detalle['item_id'],
                'tipo_item' => $detalle['tipo_item'],
                'cantidad' => $detalle['cantidad'],
            ]);
        }

        return redirect()->route('admin.compras.requerimientos.index')
            ->with('success', 'Requerimiento de compra actualizado exitosamente.');
    }

    public function destroy($id)
    {
        $requerimiento = RequerimientoCompra::findOrFail($id);
        $requerimiento->delete();

        return redirect()->route('admin.compras.requerimientos.index')
            ->with('success', 'Requerimiento de compra eliminado exitosamente.');
    }

    public function show($anyParameter)
    {
        $id = is_numeric($anyParameter) ? $anyParameter : null;
        if (!$id) {
            return redirect()->back()->with('error', 'ID de requerimiento no válido');
        }

        $requerimiento = RequerimientoCompra::with([
            'almacen', 
            'detalles.item', 
            'ordenesCompra',
            'user',
            'estado',
            'proveedor' 
        ])->findOrFail($id);
        
        return view('admin.compras.requerimientos.show', compact('requerimiento'));
    }

    public function searchPartes(Request $request)
    {
        $query = $request->input('query');

        if (!$query) {
            return response()->json([], 200);
        }

        $partes = Parte::where('codigo', 'LIKE', "%{$query}%")
            ->orWhere('nombre', 'LIKE', "%{$query}%")
            ->select('id', 'codigo', 'nombre')
            ->get()
            ->map(function ($parte) {
                return [
                    'id' => $parte->id,
                    'codigo' => $parte->codigo,
                    'nombre' => $parte->nombre,
                    'tipo' => 'parte',
                ];
            });

        $vehiculos = Vehiculo::whereHas('marca', function ($q) use ($query) {
                $q->where('nombre', 'LIKE', "%{$query}%");
            })
            ->orWhereHas('modelo', function ($q) use ($query) {
                $q->where('nombre', 'LIKE', "%{$query}%");
            })
            ->with(['marca', 'modelo', 'version', 'anioModelo'])
            ->get()
            ->map(function ($vehiculo) {
                $codigo = "V{$vehiculo->id}";
                $nombre = implode(' ', array_filter([
                    $vehiculo->marca?->nombre ?? '',
                    $vehiculo->modelo?->nombre ?? '',
                    $vehiculo->version?->nombre ?? '',
                    $vehiculo->anioModelo?->anio ?? '',
                ]));
                return [
                    'id' => $vehiculo->id,
                    'codigo' => $codigo,
                    'nombre' => $nombre ?: 'Vehículo sin descripción',
                    'tipo' => 'vehiculo',
                ];
            });

        $resultados = $partes->concat($vehiculos)->take(10);
        return response()->json($resultados);
    }

    // NUEVO MÉTODO: Búsqueda de proveedores
    public function searchProveedores(Request $request)
    {
        $query = $request->input('query');

        if (!$query) {
            return response()->json([], 200);
        }

        $proveedores = Proveedor::where('numero_documento', 'LIKE', "%{$query}%")
            ->orWhere('razon_social', 'LIKE', "%{$query}%")
            ->orWhere('nombres', 'LIKE', "%{$query}%")
            ->orWhere(function($q) use ($query) {
                $q->whereRaw("CONCAT(nombres, ' ', apellido_paterno, ' ', apellido_materno) LIKE ?", ["%{$query}%"]);
            })
            ->select('id', 'tipo_documento', 'numero_documento', 'razon_social', 'nombres', 'apellido_paterno', 'apellido_materno')
            ->limit(10)
            ->get()
            ->map(function ($proveedor) {
                return [
                    'id' => $proveedor->id,
                    'documento' => $proveedor->tipo_documento . ': ' . $proveedor->numero_documento,
                    'nombre_completo' => $proveedor->nombre_completo,
                    'texto_busqueda' => $proveedor->documento_formateado . ' - ' . $proveedor->nombre_completo
                ];
            });

        return response()->json($proveedores);
    }
}