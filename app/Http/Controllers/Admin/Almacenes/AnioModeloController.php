<?php

namespace App\Http\Controllers\Admin\Almacenes;

use App\Http\Controllers\Controller;
use App\Models\Marca;
use App\Models\Modelo;
use App\Models\Version;
use App\Models\AnioModelo;
use Illuminate\Http\Request;

class AnioModeloController extends Controller
{
    public function index()
    {
        $aniosModelo = AnioModelo::with(['marca', 'modelo', 'version'])->paginate(10);
        return view('admin.productos-servicios.vehiculos.caracteristicas.anios-modelo.index', compact('aniosModelo'));
    }

    public function create()
    {
        $marcas = Marca::all();
        $modelos = Modelo::all();
        $versiones = Version::all();
        return view('admin.productos-servicios.vehiculos.caracteristicas.anios-modelo.create', compact('marcas', 'modelos', 'versiones'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'marca_id' => 'required|exists:marcas,id',
            'modelo_id' => 'required|exists:modelos,id',
            'version_id' => 'required|exists:versiones,id',
            'anio' => 'required|integer|min:1900|max:' . (date('Y') + 1),
            'precio' => 'required|numeric|min:0',
            'moneda' => 'required|in:SOL,USD',
        ]);

        AnioModelo::create($request->all());

        return redirect()->route('admin.productos-servicios.vehiculos.index')
                        ->with('success', 'Año de modelo creado exitosamente.');
    }

    public function edit(AnioModelo $anioModelo)
    {
        $marcas = Marca::all();
        $modelos = Modelo::all();
        $versiones = Version::all();
        return view('admin.productos-servicios.vehiculos.caracteristicas.anios-modelo.edit', compact('anioModelo', 'marcas', 'modelos', 'versiones'));
    }

    public function update(Request $request, AnioModelo $anioModelo)
    {
        $request->validate([
            'marca_id' => 'required|exists:marcas,id',
            'modelo_id' => 'required|exists:modelos,id',
            'version_id' => 'required|exists:versiones,id',
            'anio' => 'required|integer|min:1900|max:' . (date('Y') + 1),
            'precio' => 'required|numeric|min:0',
            'moneda' => 'required|in:SOL,USD',
        ]);

        $anioModelo->update($request->all());

        return redirect()->route('admin.productos-servicios.vehiculos.index')
                        ->with('success', 'Año de modelo actualizado exitosamente.');
    }

    public function destroy(AnioModelo $anioModelo)
    {
        try {
            $anioModelo->delete();
            return redirect()->route('admin.productos-servicios.vehiculos.caracteristicas.anios-modelo.index')
                            ->with('success', 'Año de modelo eliminado exitosamente.');
        } catch (\Exception $e) {
            return redirect()->route('admin.productos-servicios.vehiculos.caracteristicas.anios-modelo.index')
                            ->with('error', 'No se pudo eliminar el año de modelo. Puede estar en uso.');
        }
    }
}