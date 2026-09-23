<?php

namespace App\Http\Controllers\Admin\Configuracion;

use App\Http\Controllers\Controller;
use App\Models\CentroCosto;
use Illuminate\Http\Request;

class CentroCostoController extends Controller
{
    public function index()
    {
        $centros = CentroCosto::paginate(10);
        $totalCentros = CentroCosto::count();

        return view('admin.configuracion.centros_costos.index', compact('centros', 'totalCentros'));
    }

    public function create()
    {
        return view('admin.configuracion.centros_costos.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'codigo' => 'required|string|max:50|unique:centros_costos',
            'nombre' => 'required|string|max:255',
            'descripcion' => 'nullable|string',
        ]);

        CentroCosto::create($request->only('codigo', 'nombre', 'descripcion'));

        return redirect()->route('admin.configuracion.centros_costos.index')
                         ->with('success', 'Centro de costo creado con éxito');
    }

    public function edit(CentroCosto $centroCosto)
    {
        return view('admin.configuracion.centros_costos.edit', compact('centroCosto'));
    }

    public function update(Request $request, CentroCosto $centroCosto)
    {
        $request->validate([
            'codigo' => 'required|string|max:50|unique:centros_costos,codigo,' . $centroCosto->id,
            'nombre' => 'required|string|max:255',
            'descripcion' => 'nullable|string',
        ]);

        $centroCosto->update($request->only('codigo', 'nombre', 'descripcion'));

        return redirect()->route('admin.configuracion.centros_costos.index')
                         ->with('success', 'Centro de costo actualizado con éxito');
    }

    public function destroy(CentroCosto $centroCosto)
    {
        $centroCosto->delete();

        return redirect()->route('admin.configuracion.centros_costos.index')
                         ->with('success', 'Centro de costo eliminado con éxito');
    }
}