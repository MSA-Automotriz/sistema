<?php

namespace App\Http\Controllers\Admin\Almacenes;

use App\Http\Controllers\Controller;
use App\Models\Servicio;
use App\Models\CategoriasServicios;
use Illuminate\Http\Request;

class ServicioController extends Controller
{
    public function index()
    {
        $servicios = Servicio::with('categoria')->paginate(10);
        $totalServicios = Servicio::count();

        return view('admin.productos-servicios.servicios.index', compact('servicios', 'totalServicios'));
    }

    public function create()
    {
        $categorias = CategoriasServicios::all();
        return view('admin.productos-servicios.servicios.create', compact('categorias'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:255|unique:servicios,nombre',
            'precio' => 'required|numeric|min:0',
            'moneda' => 'required|in:SOL,USD',
            'categoria_id' => 'required|exists:categorias_servicios_tercerizados,id',
        ]);

        Servicio::create($request->all());

        return redirect()->route('admin.almacenes.servicios-terceros.index')
                         ->with('success', 'Servicio tercerizado creado exitosamente.');
    }

    public function edit(Servicio $servicioTercerizado)
    {
        $categorias = CategoriasServicios::all();
        return view('admin.productos-servicios.servicios.edit', compact('servicioTercerizado', 'categorias'));
    }

    public function update(Request $request, Servicio $servicioTercerizado)
    {
        $request->validate([
            'nombre' => 'required|string|max:255|unique:servicios,nombre,' . $servicioTercerizado->id,
            'precio' => 'required|numeric|min:0',
            'moneda' => 'required|in:SOL,USD',
            'categoria_id' => 'required|exists:categorias_servicios_tercerizados,id',
        ]);

        $servicioTercerizado->update($request->all());

        return redirect()->route('admin.almacenes.servicios-terceros.index')
                         ->with('success', 'Servicio tercerizado actualizado exitosamente.');
    }

    public function destroy(Servicio $servicioTercerizado)
    {
        try {
            $servicioTercerizado->delete();
            return redirect()->route('admin.almacenes.servicios-terceros.index')
                             ->with('success', 'Servicio tercerizado eliminado exitosamente.');
        } catch (\Exception $e) {
            return redirect()->route('admin.almacenes.servicios-terceros.index')
                             ->with('error', 'No se pudo eliminar el servicio. Puede estar en uso.');
        }
    }
}
