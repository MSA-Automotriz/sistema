<?php

namespace App\Http\Controllers\Admin\Almacenes;

use App\Http\Controllers\Controller;
use App\Models\Combustible;
use App\Models\Marca;
use App\Models\Modelo;
use App\Models\Version;
use Illuminate\Http\Request;

class VersionController extends Controller
{
    public function index()
    {
        $versiones = Version::with(['marca', 'modelo', 'combustible'])->paginate(10);
        return view('admin.productos-servicios.vehiculos.caracteristicas.versiones.index', compact('versiones'));
    }

    public function create()
    {
        $marcas = Marca::all();
        $modelos = Modelo::all();
        $combustibles = Combustible::all();
        return view('admin.productos-servicios.vehiculos.caracteristicas.versiones.create', compact('marcas', 'modelos', 'combustibles'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'marca_id' => 'required|exists:marcas,id',
            'modelo_id' => 'required|exists:modelos,id',
            'nombre' => 'required|string|max:255',
            'carroceria' => 'required|string|max:255',
            'cilindrada' => 'required|string|max:255',
            'transmision' => 'required|string|max:255',
            'traccion' => 'required|string|max:255',
            'combustible_id' => 'required|exists:combustibles,id',
        ]);

        Version::create($request->all());

        return redirect()->route('admin.productos-servicios.vehiculos.caracteristicas.versiones.index')
                        ->with('success', 'Versión creada exitosamente.');
    }

    public function edit(Version $version)
    {
        $marcas = Marca::all();
        $modelos = Modelo::all();
        $combustibles = Combustible::all();
        return view('admin.productos-servicios.vehiculos.caracteristicas.versiones.edit', compact('version', 'marcas', 'modelos', 'combustibles'));
    }

    public function update(Request $request, Version $version)
    {
        $request->validate([
            'marca_id' => 'required|exists:marcas,id',
            'modelo_id' => 'required|exists:modelos,id',
            'nombre' => 'required|string|max:255',
            'carroceria' => 'required|string|max:255',
            'cilindrada' => 'required|string|max:255',
            'transmision' => 'required|string|max:255',
            'traccion' => 'required|string|max:255',
            'combustible_id' => 'required|exists:combustibles,id',
        ]);

        $version->update($request->all());

        return redirect()->route('admin.productos-servicios.vehiculos.caracteristicas.versiones.index')
                        ->with('success', 'Versión actualizada exitosamente.');
    }

    public function destroy(Version $version)
    {
        try {
            $version->delete();
            return redirect()->route('admin.productos-servicios.vehiculos.caracteristicas.versiones.index')
                            ->with('success', 'Versión eliminada exitosamente.');
        } catch (\Exception $e) {
            return redirect()->route('admin.productos-servicios.vehiculos.caracteristicas.versiones.index')
                            ->with('error', 'No se pudo eliminar la versión. Puede estar en uso.');
        }
    }
}