<?php

namespace App\Http\Controllers\Admin\Ventas;

use App\Http\Controllers\Controller;
use App\Models\CategoriaCliente;
use Illuminate\Http\Request;

class CategoriaClienteController extends Controller
{
    public function index()
    {
        $categorias = CategoriaCliente::paginate(10);
        return view('admin.ventas.clientes.categorías.index', compact('categorias'));
    }

    public function create()
    {
        return view('admin.ventas.clientes.categorías.create');
    }

    public function store(Request $request)
    {
        $request->validate(['nombre' => 'required|unique:categoria_clientes']);
        CategoriaCliente::create($request->all());
        return redirect()->route('admin.ventas.categorias.index')->with('success', 'Categoría creada');
    }

    public function edit(CategoriaCliente $categoria)
    {
        return view('admin.ventas.clientes.categorías.edit', compact('categoria'));
    }

    public function update(Request $request, CategoriaCliente $categoria)
    {
        $request->validate(['nombre' => 'required|unique:categoria_clientes,nombre,' . $categoria->id]);
        $categoria->update($request->all());
        return redirect()->route('admin.ventas.categorias.index')->with('success', 'Categoría actualizada');
    }

    public function destroy(CategoriaCliente $categoria)
    {
        $categoria->delete();
        return redirect()->route('admin.ventas.categorias.index')->with('success', 'Categoría eliminada');
    }
}