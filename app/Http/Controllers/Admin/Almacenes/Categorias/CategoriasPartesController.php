<?php
namespace App\Http\Controllers\Admin\Almacenes\Categorias;
use App\Http\Controllers\Controller;
use App\Models\CategoriasPartes;
use Illuminate\Http\Request;

class CategoriasPartesController extends Controller
{
    public function index()
    {
        $categorias = CategoriasPartes::paginate(10);
        $totalCategorias = CategoriasPartes::count();
        return view('admin.productos-servicios.partes-repuestos.categorias.index', compact('categorias', 'totalCategorias'));
    }
    
    public function create()
    {
        return view('admin.productos-servicios.partes-repuestos.categorias.create');
    }
    
    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:255|unique:categorias_partes',
            'descripcion' => 'nullable|string',
            'descuento' => 'required|numeric|min:0|max:100',
        ]);
        
        CategoriasPartes::create($request->only('nombre', 'descripcion', 'descuento'));
        return redirect()->route('admin.almacenes.partes.categorias.index')
                         ->with('success', 'Categoría de partes creada con éxito');
    }
    
    public function edit(CategoriasPartes $categoriaParte)
    {
        return view('admin.productos-servicios.partes-repuestos.categorias.edit', compact('categoriaParte'));
    }
    
    public function update(Request $request, CategoriasPartes $categoriaParte)
    {
        $request->validate([
            'nombre' => 'required|string|max:255|unique:categorias_partes,nombre,' . $categoriaParte->id,
            'descripcion' => 'nullable|string',
            'descuento' => 'required|numeric|min:0|max:100',
        ]);
        
        $categoriaParte->update($request->only('nombre', 'descripcion', 'descuento'));
        return redirect()->route('admin.almacenes.partes.categorias.index')
                         ->with('success', 'Categoría de partes actualizada con éxito');
    }
    
    public function destroy(CategoriasPartes $categoriaParte)
    {
        $categoriaParte->delete();
        return redirect()->route('admin.almacenes.partes.categorias.index')
                         ->with('success', 'Categoría de partes eliminada con éxito');
    }
}