<?php

namespace App\Http\Controllers\Admin\Configuracion\Maestros;

use App\Http\Controllers\Controller;
use App\Models\Banco;
use Illuminate\Http\Request;

class BancoController extends Controller
{
    public function index()
    {
        $bancos = Banco::paginate(10);
        return view('admin.configuracion.maestros.bancos.index', compact('bancos'));
    }

    public function create()
    {
        return view('admin.configuracion.maestros.bancos.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:255|unique:bancos,nombre',
        ]);

        Banco::create($request->all());

        return redirect()->route('admin.configuracion.maestros.bancos.index')
                        ->with('success', 'Banco creado exitosamente.');
    }

    public function edit(Banco $banco)
    {
        return view('admin.configuracion.maestros.bancos.edit', compact('banco'));
    }

    public function update(Request $request, Banco $banco)
    {
        $request->validate([
            'nombre' => 'required|string|max:255|unique:bancos,nombre,' . $banco->id,
        ]);

        $banco->update($request->all());

        return redirect()->route('admin.configuracion.maestros.bancos.index')
                        ->with('success', 'Banco actualizado exitosamente.');
    }

    public function destroy(Banco $banco)
    {
        try {
            $banco->delete();
            return redirect()->route('admin.configuracion.maestros.bancos.index')
                            ->with('success', 'Banco eliminado exitosamente.');
        } catch (\Exception $e) {
            return redirect()->route('admin.configuracion.maestros.bancos.index')
                            ->with('error', 'No se pudo eliminar el banco. Puede estar en uso.');
        }
    }
}