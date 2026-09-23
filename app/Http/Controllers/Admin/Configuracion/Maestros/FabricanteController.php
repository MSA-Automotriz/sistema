<?php

namespace App\Http\Controllers\Admin\Configuracion\Maestros;

use App\Http\Controllers\Controller;
use App\Models\Fabricante;
use Illuminate\Http\Request;

class FabricanteController extends Controller
{
    public function index()
    {
        $fabricantes = Fabricante::paginate(10);
        $totalFabricantes = Fabricante::count();

        return view('admin.configuracion.maestros.fabricantes.index', compact('fabricantes', 'totalFabricantes'));
    }

    public function create()
    {
        return view('admin.configuracion.maestros.fabricantes.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nombre_fabricante' => 'required|string|max:255|unique:fabricantes',
        ]);

        Fabricante::create($request->only('nombre_fabricante'));

        return redirect()->route('admin.configuracion.maestros.fabricantes.index')
                         ->with('success', 'Fabricante creado con éxito');
    }

    public function edit(Fabricante $fabricante)
    {
        return view('admin.configuracion.maestros.fabricantes.edit', compact('fabricante'));
    }

    public function update(Request $request, Fabricante $fabricante)
    {
        $request->validate([
            'nombre_fabricante' => 'required|string|max:255|unique:fabricantes,nombre_fabricante,' . $fabricante->id,
        ]);

        $fabricante->update($request->only('nombre_fabricante'));

        return redirect()->route('admin.configuracion.maestros.fabricantes.index')
                         ->with('success', 'Fabricante actualizado con éxito');
    }

    public function destroy(Fabricante $fabricante)
    {
        $fabricante->delete();

        return redirect()->route('admin.configuracion.maestros.fabricantes.index')
                         ->with('success', 'Fabricante eliminado con éxito');
    }
}