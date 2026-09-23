<?php

namespace App\Http\Controllers\Admin\Almacenes;

use App\Http\Controllers\Controller;
use App\Models\Color;
use Illuminate\Http\Request;

class ColorController extends Controller
{
    public function index()
    {
        $colores = Color::paginate(10);
        return view('admin.productos-servicios.vehiculos.caracteristicas.colores.index', compact('colores'));
    }

    public function create()
    {
        return view('admin.productos-servicios.vehiculos.caracteristicas.colores.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:255|unique:colores,nombre',
            'hexadecimal' => 'required|regex:/^#[0-9A-Fa-f]{6}$/',
        ]);

        Color::create($request->all());

        return redirect()->route('admin.productos-servicios.vehiculos.index')
                        ->with('success', 'Color creado exitosamente.');
    }

    public function edit(Color $color)
    {
        return view('admin.productos-servicios.vehiculos.caracteristicas.colores.edit', compact('color'));
    }

    public function update(Request $request, Color $color)
    {
        $request->validate([
            'nombre' => 'required|string|max:255|unique:colores,nombre,' . $color->id,
            'hexadecimal' => 'required|regex:/^#[0-9A-Fa-f]{6}$/',
        ]);

        $color->update($request->all());

        return redirect()->route('admin.productos-servicios.vehiculos.index')
                        ->with('success', 'Color actualizado exitosamente.');
    }

    public function destroy(Color $color)
    {
        try {
            $color->delete();
            return redirect()->route('admin.productos-servicios.vehiculos.caracteristicas.colores.index')
                            ->with('success', 'Color eliminado exitosamente.');
        } catch (\Exception $e) {
            return redirect()->route('admin.productos-servicios.vehiculos.caracteristicas.colores.index')
                            ->with('error', 'No se pudo eliminar el color. Puede estar en uso.');
        }
    }
}