<?php
namespace App\Http\Controllers\Admin\Almacenes;
use App\Http\Controllers\Controller;
use App\Models\Parte;
use App\Models\Unidad;
use App\Models\Fabricante;
use App\Models\CategoriasPartes;
use App\Models\Proveedor;
use Illuminate\Http\Request;

class ParteController extends Controller
{
    public function index()
    {
        $partes = Parte::with(['unidad', 'fabricante', 'categoriaParte', 'proveedor'])->paginate(10); // Agregamos proveedor
        $totalPartes = Parte::count();
        return view('admin.productos-servicios.partes-repuestos.index', compact('partes', 'totalPartes'));
    }
    
    public function create()
    {
        $unidades = Unidad::all();
        $fabricantes = Fabricante::all();
        $categorias = CategoriasPartes::all();
        $proveedores = Proveedor::orderBy('razon_social')->get(); // Agregamos esto
        $ultimoCodigo = Parte::max('codigo');
        $nuevoCodigo = $ultimoCodigo ? str_pad((int)$ultimoCodigo + 1, 6, '0', STR_PAD_LEFT) : '000001';
        return view('admin.productos-servicios.partes-repuestos.create', compact('unidades', 'fabricantes', 'categorias', 'nuevoCodigo', 'proveedores'));
    }
    
    public function store(Request $request)
    {
        $request->validate([
            'codigo' => 'required|string|max:255|unique:partes,codigo',
            'autogenerar_codigo' => 'required|boolean',
            'nombre' => 'required|string|max:255',
            'unidad_id' => 'required|exists:unidades,id',
            'fabricante_id' => 'nullable|exists:fabricantes,id',
            'precio_venta' => 'required|numeric|min:0',
            'moneda_venta' => 'required|in:SOL,USD',
            'precio_compra' => 'required|numeric|min:0',
            'moneda_compra' => 'required|in:SOL,USD',
            'categoria_parte_id' => 'required|exists:categorias_partes,id',
            'proveedor_id' => 'required|exists:proveedores,id', 
        ]);
        
        Parte::create($request->all());
        return redirect()->route('admin.almacenes.partes.index')
                        ->with('success', 'Parte creada exitosamente.');
    }
    
    public function edit(Parte $parte)
    {
        $unidades = Unidad::all();
        $fabricantes = Fabricante::all();
        $categorias = CategoriasPartes::all();
        $proveedores = Proveedor::orderBy('razon_social')->get(); 
        return view('admin.productos-servicios.partes-repuestos.edit', compact('parte', 'unidades', 'fabricantes', 'categorias', 'proveedores'));
    }
    
    public function update(Request $request, Parte $parte)
    {
        $request->validate([
            'codigo' => 'required|string|max:255|unique:partes,codigo,' . $parte->id,
            'autogenerar_codigo' => 'required|boolean',
            'nombre' => 'required|string|max:255',
            'unidad_id' => 'required|exists:unidades,id',
            'fabricante_id' => 'nullable|exists:fabricantes,id',
            'precio_venta' => 'required|numeric|min:0',
            'moneda_venta' => 'required|in:SOL,USD',
            'precio_compra' => 'required|numeric|min:0',
            'moneda_compra' => 'required|in:SOL,USD',
            'categoria_parte_id' => 'required|exists:categorias_partes,id',
            'proveedor_id' => 'required|exists:proveedores,id', 
        ]);
        
        $parte->update($request->all());
        return redirect()->route('admin.almacenes.partes.index')
                        ->with('success', 'Parte actualizada exitosamente.');
    }
    
    public function destroy(Parte $parte)
    {
        try {
            $parte->delete();
            return redirect()->route('admin.almacenes.partes.index')
                            ->with('success', 'Parte eliminada exitosamente.');
        } catch (\Exception $e) {
            return redirect()->route('admin.almacenes.partes.index')
                            ->with('error', 'No se pudo eliminar la parte. Puede estar en uso.');
        }
    }

    public function buscarPorCodigo(Request $request)
    {
        $codigo = trim($request->get('codigo', ''));
        if (!$codigo) {
            return response()->json(['found' => false, 'message' => 'Código vacío.']);
        }

        $parte = Parte::with(['unidad', 'categoriaParte'])
            ->where('codigo', $codigo)
            ->first();

        if (!$parte) {
            return response()->json(['found' => false, 'message' => 'Producto no encontrado con código: ' . $codigo]);
        }

        return response()->json([
            'found' => true,
            'parte' => [
                'id'          => $parte->id,
                'codigo'      => $parte->codigo,
                'nombre'      => $parte->nombre,
                'precio_venta'=> (float) $parte->precio_venta,
                'moneda_venta'=> $parte->moneda_venta,
                'unidad'      => $parte->unidad->nombre ?? 'N/A',
                'categoria'   => $parte->categoriaParte->nombre ?? 'N/A',
            ],
        ]);
    }
}