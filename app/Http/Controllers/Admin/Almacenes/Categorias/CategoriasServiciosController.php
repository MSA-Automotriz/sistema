<?php

namespace App\Http\Controllers\Admin\Almacenes\Categorias;

use App\Http\Controllers\Controller;
use App\Models\CategoriasServicios;
use Illuminate\Http\Request;

class CategoriasServiciosController extends Controller
{
    /**
     * Mostrar listado de categorías
     */
    public function index()
    {
        $categorias = CategoriasServicios::paginate(10);
        $totalCategorias = CategoriasServicios::count();
        return view('admin.productos-servicios.servicios.categorias.index', compact('categorias', 'totalCategorias'));
    }

    /**
     * Mostrar formulario de creación
     */
    public function create()
    {
        return view('admin.productos-servicios.servicios.categorias.create');
    }

    /**
     * Almacenar una nueva categoría
     */
    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:255|unique:categorias_servicios_tercerizados'
        ]);

        CategoriasServicios::create($request->only('nombre'));
        return redirect()->route('admin.productos-servicios.servicios.index')
                         ->with('success', 'Categoría creada con éxito');
    }

    /**
     * Mostrar formulario de edición
     */
    public function edit(CategoriasServicios $categoria)
    {
        return view('admin.productos-servicios.servicios.categorias.edit', compact('categoria'));
    }

    /**
     * Actualizar categoría
     */
    public function update(Request $request, CategoriasServicios $categoria)
    {
        $request->validate([
            'nombre' => 'required|string|max:255|unique:categorias_servicios_tercerizados,nombre,' . $categoria->id
        ]);

        $categoria->update($request->only('nombre'));
        return redirect()->route('admin.productos-servicios.servicios.index')
                         ->with('success', 'Categoría actualizada con éxito');
    }

    /**
     * Eliminar categoría
     */
    public function destroy(CategoriasServicios $categoria)
    {
        // Verificamos si tiene servicios asociados
        if ($categoria->serviciosTercerizados()->count() > 0) {
            return redirect()->route('admin.productos-servicios.servicios.index')
                             ->with('error', 'No se puede eliminar la categoría porque tiene servicios asociados');
        }

        $categoria->delete();
        return redirect()->route('admin.productos-servicios.servicios.index')
                         ->with('success', 'Categoría eliminada con éxito');
    }
}