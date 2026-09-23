<?php

namespace App\Http\Controllers\Admin\Almacenes;

use App\Http\Controllers\Controller;
use App\Models\Marca;
use App\Models\Modelo;
use Illuminate\Http\Request;

class ModeloController extends Controller
{
    public function index()
    {
        $modelos = Modelo::with('marca')->paginate(10);
        return view('admin.productos-servicios.vehiculos.caracteristicas.modelos.index', compact('modelos'));
    }

    public function create()
    {
        $marcas = Marca::all();
        return view('admin.productos-servicios.vehiculos.caracteristicas.modelos.create', compact('marcas'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'marca_id' => 'required|exists:marcas,id',
            'nombre' => 'required|string|max:255',
            'duracion_garantia' => 'nullable|string|max:255',
            'cantidad_anos' => 'required|integer|min:1',
            'ficha_tecnica' => 'nullable|file|mimes:pdf|max:2048',
        ]);

        $data = $request->all();

        if ($request->hasFile('ficha_tecnica')) {
            $data['ficha_tecnica'] = $request->file('ficha_tecnica')->store('fichas_tecnicas', 'public');
        }

        Modelo::create($data);

        return redirect()->route('admin.productos-servicios.vehiculos.index')
                        ->with('success', 'Modelo creado exitosamente.');
    }

    public function edit(Modelo $modelo)
    {
        $marcas = Marca::all();
        return view('admin.productos-servicios.vehiculos.caracteristicas.modelos.edit', compact('modelo', 'marcas'));
    }

    public function update(Request $request, Modelo $modelo)
    {
        $request->validate([
            'marca_id' => 'required|exists:marcas,id',
            'nombre' => 'required|string|max:255',
            'duracion_garantia' => 'nullable|string|max:255',
            'cantidad_anos' => 'required|integer|min:1',
            'ficha_tecnica' => 'nullable|file|mimes:pdf|max:2048',
        ]);

        $data = $request->all();

        if ($request->hasFile('ficha_tecnica')) {
            $data['ficha_tecnica'] = $request->file('ficha_tecnica')->store('fichas_tecnicas', 'public');
        }

        $modelo->update($data);

        return redirect()->route('admin.productos-servicios.vehiculos.index')
                        ->with('success', 'Modelo actualizado exitosamente.');
    }

    public function destroy(Modelo $modelo)
    {
        try {
            $modelo->delete();
            return redirect()->route('admin.productos-servicios.vehiculos.caracteristicas.modelos.index')
                            ->with('success', 'Modelo eliminado exitosamente.');
        } catch (\Exception $e) {
            return redirect()->route('admin.productos-servicios.vehiculos.caracteristicas.modelos.index')
                            ->with('error', 'No se pudo eliminar el modelo. Puede estar en uso.');
        }
    }
}