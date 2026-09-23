<?php

namespace App\Http\Controllers\Admin\Configuracion\Maestros;

use App\Http\Controllers\Controller;
use App\Models\Cargo;
use Illuminate\Http\Request;

class CargoController extends Controller
{
    public function index()
    {
        $cargos = Cargo::paginate(10);
        $totalCargos = Cargo::count();

        return view('admin.configuracion.maestros.cargos.index', compact('cargos', 'totalCargos'));
    }

    public function create()
    {
        return view('admin.configuracion.maestros.cargos.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nombre_cargo' => 'required|string|max:255|unique:cargos',
        ]);

        Cargo::create($request->only('nombre_cargo'));

        return redirect()->route('admin.configuracion.maestros.cargos.index')
                         ->with('success', 'Cargo creado con éxito');
    }

    public function edit(Cargo $cargo)
    {
        return view('admin.configuracion.maestros.cargos.edit', compact('cargo'));
    }

    public function update(Request $request, Cargo $cargo)
    {
        $request->validate([
            'nombre_cargo' => 'required|string|max:255|unique:cargos,nombre_cargo,' . $cargo->id,
        ]);

        $cargo->update($request->only('nombre_cargo'));

        return redirect()->route('admin.configuracion.maestros.cargos.index')
                         ->with('success', 'Cargo actualizado con éxito');
    }

    public function destroy(Cargo $cargo)
    {
        $cargo->delete();

        return redirect()->route('admin.configuracion.maestros.cargos.index')
                         ->with('success', 'Cargo eliminado con éxito');
    }
}