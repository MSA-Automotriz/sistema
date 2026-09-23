<?php

namespace App\Http\Controllers\Admin\Almacenes;

use App\Http\Controllers\Controller;
use App\Models\Marca;
use Illuminate\Http\Request;

class MarcaController extends Controller
{
    public function index()
    {
        $marcas = Marca::paginate(10);
        return view('admin.productos-servicios.vehiculos.caracteristicas.marcas.index', compact('marcas'));
    }

    public function create()
    {
        return view('admin.productos-servicios.vehiculos.caracteristicas.marcas.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:255|unique:marcas,nombre',
        ]);

        Marca::create($request->all());

        return redirect()->route('admin.productos-servicios.vehiculos.index')
                        ->with('success', 'Marca creada exitosamente.');
    }

    public function edit(Marca $marca)
    {
        return view('admin.productos-servicios.vehiculos.caracteristicas.marcas.edit', compact('marca'));
    }

    public function update(Request $request, Marca $marca)
    {
        $request->validate([
            'nombre' => 'required|string|max:255|unique:marcas,nombre,' . $marca->id,
        ]);

        $marca->update($request->all());

        return redirect()->route('admin.productos-servicios.vehiculos.index')
                        ->with('success', 'Marca actualizada exitosamente.');
    }

    public function destroy(Marca $marca)
    {
        try {
            $marca->delete();
            return redirect()->route('admin.productos-servicios.vehiculos.caracteristicas.marcas.index')
                            ->with('success', 'Marca eliminada exitosamente.');
        } catch (\Exception $e) {
            return redirect()->route('admin.productos-servicios.vehiculos.caracteristicas.marcas.index')
                            ->with('error', 'No se pudo eliminar la marca. Puede estar en uso.');
        }
    }
}