<?php

namespace App\Http\Controllers\Admin\Almacenes;

use App\Http\Controllers\Controller;
use App\Models\Almacen;
use App\Models\CentroCosto;
use Illuminate\Http\Request;

class AlmacenController extends Controller
{
    public function index()
    {
        // Cargar almacenes de nivel raíz con sus hijos recursivamente
        $almacenes = Almacen::with('allChildren')->whereNull('parent_id')->paginate(10);
        $totalAlmacenes = Almacen::count();

        return view('admin.almacenes.index', compact('almacenes', 'totalAlmacenes'));
    }

    public function create()
    {
        $centrosCostos = CentroCosto::all();
        $almacenes = Almacen::with('allChildren')->whereNull('parent_id')->get(); // Para el selector de padre
        return view('admin.almacenes.create', compact('centrosCostos', 'almacenes'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:255|unique:almacenes,nombre',
            'direccion' => 'required|string|max:255',
            'es_vehiculos' => 'required|boolean',
            'centro_costo_id' => 'required|exists:centros_costos,id',
            'parent_id' => 'nullable|exists:almacenes,id',
        ]);

        Almacen::create([
            'nombre' => $request->nombre,
            'direccion' => $request->direccion,
            'es_vehiculos' => $request->es_vehiculos,
            'centro_costo_id' => $request->centro_costo_id,
            'parent_id' => $request->parent_id,
        ]);

        return redirect()->route('admin.almacenes.index')
                        ->with('success', 'Almacén creado exitosamente.');
    }

    public function edit(Almacen $almacen)
    {
        $centrosCostos = CentroCosto::all();
        $almacenes = Almacen::with('allChildren')->whereNull('parent_id')->get(); // Para el selector de padre
        return view('admin.almacenes.edit', compact('almacen', 'centrosCostos', 'almacenes'));
    }

    public function update(Request $request, Almacen $almacen)
    {
        $request->validate([
            'nombre' => 'required|string|max:255|unique:almacenes,nombre,' . $almacen->id,
            'direccion' => 'required|string|max:255',
            'es_vehiculos' => 'required|boolean',
            'centro_costo_id' => 'required|exists:centros_costos,id',
            'parent_id' => 'nullable|exists:almacenes,id',
        ]);

        $almacen->update([
            'nombre' => $request->nombre,
            'direccion' => $request->direccion,
            'es_vehiculos' => $request->es_vehiculos,
            'centro_costo_id' => $request->centro_costo_id,
            'parent_id' => $request->parent_id,
        ]);

        return redirect()->route('admin.almacenes.index')
                        ->with('success', 'Almacén actualizado exitosamente.');
    }

    public function destroy(Almacen $almacen)
    {
        try {
            $almacen->delete();
            return redirect()->route('admin.almacenes.index')
                            ->with('success', 'Almacén eliminado exitosamente.');
        } catch (\Exception $e) {
            return redirect()->route('admin.almacenes.index')
                            ->with('error', 'No se pudo eliminar el almacén. Puede estar en uso.');
        }
    }
}