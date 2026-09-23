<?php

namespace App\Http\Controllers\Admin\Talleres;

use App\Http\Controllers\Controller;
use App\Models\Taller;
use Illuminate\Http\Request;

class TallerController extends Controller
{
    public function index()
    {
        $talleres = Taller::paginate(10);
        $totalTalleres = Taller::count();

        return view('admin.talleres.index', compact('talleres', 'totalTalleres'));
    }

    public function create()
    {
        return view('admin.talleres.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nombre_taller' => 'required|string|max:255',
            'departamento' => 'required|string|max:255',
            'provincia' => 'required|string|max:255',
            'distrito' => 'required|string|max:255',
            'direccion' => 'required|string|max:255',
            'coordenadas' => 'required|string|max:255',
        ]);

        Taller::create($request->only([
            'nombre_taller',
            'departamento',
            'provincia',
            'distrito',
            'direccion',
            'coordenadas',
        ]));

        return redirect()->route('admin.talleres.index')
                         ->with('success', 'Taller creado con éxito');
    }

    public function edit(Taller $taller)
    {
        return view('admin.talleres.edit', compact('taller'));
    }

    public function update(Request $request, Taller $taller)
    {
        $request->validate([
            'nombre_taller' => 'required|string|max:255',
            'departamento' => 'required|string|max:255',
            'provincia' => 'required|string|max:255',
            'distrito' => 'required|string|max:255',
            'direccion' => 'required|string|max:255',
            'coordenadas' => 'required|string|max:255',
        ]);

        $taller->update($request->only([
            'nombre_taller',
            'departamento',
            'provincia',
            'distrito',
            'direccion',
            'coordenadas',
        ]));

        return redirect()->route('admin.talleres.index')
                         ->with('success', 'Taller actualizado con éxito');
    }

    public function destroy(Taller $taller)
    {
        $taller->delete();

        return redirect()->route('admin.talleres.index')
                         ->with('success', 'Taller eliminado con éxito');
    }
}