<?php

namespace App\Http\Controllers\Admin\Almacenes\Categorias;

use App\Http\Controllers\Controller;
use App\Models\ClasificacionVehiculo;
use Illuminate\Http\Request;

class ClasificacionVehiculoController extends Controller
{
    public function index()
    {
        $clasificaciones = ClasificacionVehiculo::paginate(10);
        return view('admin.productos-servicios.vehiculos.caracteristicas.catalogos.vehiculo_catalogos.index', compact('clasificaciones'));
    }

    public function create()
    {
        return view('admin.productos-servicios.vehiculos.caracteristicas.catalogos.vehiculo_catalogos.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:255|unique:clasificaciones_vehiculos,nombre',
        ]);

        ClasificacionVehiculo::create($request->all());

        return redirect()->route('admin.productos-servicios.vehiculos.caracteristicas.catalogos.vehiculo_catalogos.index')
                         ->with('success', 'Clasificación creada exitosamente.');
    }

    public function edit(ClasificacionVehiculo $clasificacionVehiculo)
    {
        return view('admin.productos-servicios.vehiculos.caracteristicas.catalogos.vehiculo_catalogos.edit', compact('clasificacionVehiculo'));
    }

    public function update(Request $request, ClasificacionVehiculo $clasificacionVehiculo)
    {
        $request->validate([
            'nombre' => 'required|string|max:255|unique:clasificaciones_vehiculos,nombre,' . $clasificacionVehiculo->id,
        ]);

        $clasificacionVehiculo->update($request->all());

        return redirect()->route('admin.productos-servicios.vehiculos.caracteristicas.catalogos.vehiculo_catalogos.index')
                         ->with('success', 'Clasificación actualizada exitosamente.');
    }

    public function destroy(ClasificacionVehiculo $clasificacionVehiculo)
    {
        try {
            $clasificacionVehiculo->delete();
            return redirect()->route('admin.productos-servicios.vehiculos.caracteristicas.catalogos.vehiculo_catalogos.index')
                             ->with('success', 'Clasificación eliminada exitosamente.');
        } catch (\Exception $e) {
            return redirect()->route('admin.productos-servicios.vehiculos.caracteristicas.catalogos.vehiculo_catalogos.index')
                             ->with('error', 'No se pudo eliminar la clasificación. Puede estar en uso.');
        }
    }
}