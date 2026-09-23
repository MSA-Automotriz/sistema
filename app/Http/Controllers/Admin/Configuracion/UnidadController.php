<?php

namespace App\Http\Controllers\Admin\Configuracion;

use App\Http\Controllers\Controller;
use App\Models\Unidad;
use Illuminate\Http\Request;

class UnidadController extends Controller
{
    public function index()
    {
        $unidades = Unidad::paginate(10);
        $totalUnidades = Unidad::count();

        return view('admin.configuracion.maestros.unidades.index', compact('unidades', 'totalUnidades'));
    }

    public function create()
    {
        return view('admin.configuracion.maestros.unidades.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:255|unique:unidades,nombre',
            'descripcion' => 'nullable|string',
        ]);

        Unidad::create($request->all());

        return redirect()->route('admin.configuracion.unidades.index')
                        ->with('success', 'Unidad creada exitosamente.');
    }

    public function edit(Unidad $unidad)
    {
        return view('admin.configuracion.maestros.unidades.edit', compact('unidad'));
    }

    public function update(Request $request, Unidad $unidad)
    {
        $request->validate([
            'nombre' => 'required|string|max:255|unique:unidades,nombre,' . $unidad->id,
            'descripcion' => 'nullable|string',
        ]);

        $unidad->update($request->all());

        return redirect()->route('admin.configuracion.unidades.index')
                        ->with('success', 'Unidad actualizada exitosamente.');
    }

    public function destroy(Unidad $unidad)
    {
        try {
            $unidad->delete();
            return redirect()->route('admin.configuracion.unidades.index')
                            ->with('success', 'Unidad eliminada exitosamente.');
        } catch (\Exception $e) {
            return redirect()->route('admin.configuracion.unidades.index')
                            ->with('error', 'No se pudo eliminar la unidad. Puede estar en uso.');
        }
    }
}