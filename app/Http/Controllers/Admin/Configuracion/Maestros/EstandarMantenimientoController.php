<?php

namespace App\Http\Controllers\Admin\Configuracion\Maestros;

use App\Http\Controllers\Controller;
use App\Models\EstandarMantenimiento;
use Illuminate\Http\Request;

class EstandarMantenimientoController extends Controller
{
    public function index()
    {
        $estandares = EstandarMantenimiento::paginate(10);
        $totalEstandares = EstandarMantenimiento::count();

        return view('admin.configuracion.maestros.estandar_mantenimiento.index', compact('estandares', 'totalEstandares'));
    }

    public function create()
    {
        return view('admin.configuracion.maestros.estandar_mantenimiento.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'estandar_mantenimiento' => 'required|string|max:255|unique:estandar_mantenimientos',
        ]);

        EstandarMantenimiento::create($request->only('estandar_mantenimiento'));

        return redirect()->route('admin.configuracion.maestros.estandar_mantenimiento.index')
                         ->with('success', 'Estándar de Mantenimiento creado con éxito');
    }

    public function edit(EstandarMantenimiento $estandarMantenimiento)
    {
        return view('admin.configuracion.maestros.estandar_mantenimiento.edit', compact('estandarMantenimiento'));
    }

    public function update(Request $request, EstandarMantenimiento $estandarMantenimiento)
    {
        $request->validate([
            'estandar_mantenimiento' => 'required|string|max:255|unique:estandar_mantenimientos,estandar_mantenimiento,' . $estandarMantenimiento->id,
        ]);

        $estandarMantenimiento->update($request->only('estandar_mantenimiento'));

        return redirect()->route('admin.configuracion.maestros.estandar_mantenimiento.index')
                         ->with('success', 'Estándar de Mantenimiento actualizado con éxito');
    }

    public function destroy(EstandarMantenimiento $estandarMantenimiento)
    {
        $estandarMantenimiento->delete();

        return redirect()->route('admin.configuracion.maestros.estandar_mantenimiento.index')
                         ->with('success', 'Estándar de Mantenimiento eliminado con éxito');
    }
}