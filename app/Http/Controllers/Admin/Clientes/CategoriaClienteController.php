<?php

namespace App\Http\Controllers\Admin\Clientes;

use App\Http\Controllers\Controller;
use App\Models\CategoriaCliente;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;

class CategoriaClienteController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $categorias = CategoriaCliente::withCount('clientes')->paginate(10);
        return view('admin.clientes.categorias.index', compact('categorias'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.clientes.categorias.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:255|unique:categoria_clientes,nombre',
        ]);

        CategoriaCliente::create([
            'nombre' => $request->nombre,
        ]);

        return Redirect::route('admin.clientes.categorias.index')
            ->with('success', 'Categoría creada exitosamente.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(CategoriaCliente $categoriaCliente)
    {
        return view('admin.clientes.categorias.edit', compact('categoriaCliente'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, CategoriaCliente $categoriaCliente)
    {
        $request->validate([
            'nombre' => 'required|string|max:255|unique:categoria_clientes,nombre,' . $categoriaCliente->id,
        ]);

        $categoriaCliente->update([
            'nombre' => $request->nombre,
        ]);

        return Redirect::route('admin.clientes.categorias.index')
            ->with('success', 'Categoría actualizada exitosamente.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(CategoriaCliente $categoriaCliente)
    {
        if ($categoriaCliente->clientes()->count() > 0) {
            return Redirect::route('admin.clientes.categorias.index')
                ->with('error', 'No se puede eliminar la categoría porque tiene clientes asociados.');
        }

        $categoriaCliente->delete();

        return Redirect::route('admin.clientes.categorias.index')
            ->with('success', 'Categoría eliminada exitosamente.');
    }
}