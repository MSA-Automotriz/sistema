<?php

namespace App\Http\Controllers\Admin\Compras;

use App\Http\Controllers\Controller;
use App\Models\CategoriaProveedor;
use Illuminate\Http\Request;

class CategoriaProveedorController extends Controller
{
    public function index()
    {
        $categorias = CategoriaProveedor::paginate(10);
        $totalCategorias = CategoriaProveedor::count();

        return view('admin.compras.proveedores.categorias.index', compact('categorias', 'totalCategorias'));
    }

    public function create()
    {
        return view('admin.compras.proveedores.categorias.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nombre_categoria_proveedor' => 'required|string|max:255|unique:categorias_proveedor',
        ]);

        CategoriaProveedor::create($request->only('nombre_categoria_proveedor'));

        return redirect()->route('admin.compras.proveedores.categorias.index')
                         ->with('success', 'Categoría de proveedor creada con éxito');
    }

    public function edit(CategoriaProveedor $categoriaProveedor)
    {
        return view('admin.compras.proveedores.categorias.edit', compact('categoriaProveedor'));
    }

    public function update(Request $request, CategoriaProveedor $categoriaProveedor)
    {
        $request->validate([
            'nombre_categoria_proveedor' => 'required|string|max:255|unique:categorias_proveedor,nombre_categoria_proveedor,' . $categoriaProveedor->id,
        ]);

        $categoriaProveedor->update($request->only('nombre_categoria_proveedor'));

        return redirect()->route('admin.compras.proveedores.categorias.index')
                         ->with('success', 'Categoría de proveedor actualizada con éxito');
    }

    public function destroy(CategoriaProveedor $categoriaProveedor)
    {
        $categoriaProveedor->delete();

        return redirect()->route('admin.compras.proveedores.categorias.index')
                         ->with('success', 'Categoría de proveedor eliminada con éxito');
    }
}
