<?php

namespace App\Http\Controllers\Admin\Almacenes;

use App\Http\Controllers\Controller;
use App\Models\Marca;
use App\Models\Modelo;
use App\Models\Version;
use App\Models\AnioModelo;
use App\Models\Vehiculo;
use App\Models\Color;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class VehiculoController extends Controller
{
    // Vista principal con pestañas
    public function vehiculosIndex()
    {
        $Vehiculos = Vehiculo::with(['marca', 'modelo', 'version', 'anioModelo'])->paginate(10);
        $marcas = Marca::paginate(10);
        $modelos = Modelo::paginate(10);
        $versiones = Version::paginate(10);
        $aniosModelo = AnioModelo::paginate(10);
        $colores = Color::paginate(10);

        return view('admin.productos-servicios.vehiculos.pestanas', compact(
            'Vehiculos', 'marcas', 'modelos', 'versiones', 'aniosModelo', 'colores'
        ));
    }

    // CRUD para Vehículos Catálogo (dentro de la pestaña)
    public function index()
    {
        $Vehiculos = Vehiculo::with(['marca', 'modelo', 'version', 'anioModelo'])->paginate(10);

        return view('admin.productos-servicios.vehiculos.caracteristicas.vehiculo.index', compact('Vehiculos'));
    }

    public function create()
    {
        $marcas = Marca::all();
        $modelos = Modelo::all();
        $versiones = Version::all();
        $aniosModelo = AnioModelo::all();

        return view('admin.productos-servicios.vehiculos.caracteristicas.vehiculo.create', compact(
            'marcas', 'modelos', 'versiones', 'aniosModelo'
        ));
    }

    public function store(Request $request)
    {
        // Validación
        $request->validate([
            'marca_id' => 'required|exists:marcas,id',
            'modelo_id' => 'required|exists:modelos,id',
            'version_id' => 'required|exists:versiones,id',
            'anio_modelo_id' => 'required|exists:anios_modelo,id',
            'fotografia' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);
    
        $data = $request->only(['marca_id', 'modelo_id', 'version_id', 'anio_modelo_id']);
    
        // Manejar la subida de archivo
        if ($request->hasFile('fotografia')) {
            $file = $request->file('fotografia');
            
            // Verifica si el archivo es válido
            if ($file->isValid()) {
                $path = $file->store('vehiculos', 'public');
                $data['fotografia'] = $path;
            } else {
                return back()->with('error', 'El archivo de imagen no es válido.');
            }
        }
    
        Vehiculo::create($data);
    
        return redirect()->route('admin.productos-servicios.vehiculos.index')
                        ->with('success', 'Vehículo catálogo creado exitosamente.');
    }

    public function edit(Vehiculo $Vehiculo)
    {
        $marcas = Marca::all();
        $modelos = Modelo::all();
        $versiones = Version::all();
        $aniosModelo = AnioModelo::all();

        return view('admin.productos-servicios.vehiculos.caracteristicas.vehiculo.edit', compact(
            'Vehiculo', 'marcas', 'modelos', 'versiones', 'aniosModelo'
        ));
    }

    public function update(Request $request, Vehiculo $Vehiculo)
    {
        // Validación
        $request->validate([
            'marca_id' => 'required|exists:marcas,id',
            'modelo_id' => 'required|exists:modelos,id',
            'version_id' => 'required|exists:versiones,id',
            'anio_modelo_id' => 'required|exists:anios_modelo,id',
            'fotografia' => 'nullable|image|mimes:jpeg,png,jpg|max:2048', // Máximo 2MB
        ]);

        $data = $request->only(['marca_id', 'modelo_id', 'version_id', 'anio_modelo_id']);

        // Manejar la subida de archivo
        if ($request->hasFile('fotografia')) {
            // Eliminar la imagen anterior si existe
            if ($Vehiculo->fotografia) {
                Storage::disk('public')->delete($Vehiculo->fotografia);
            }
            $path = $request->file('fotografia')->store('vehiculos', 'public');
            $data['fotografia'] = $path;
        }

        $Vehiculo->update($data);

        return redirect()->route('admin.productos-servicios.vehiculos.index')
                        ->with('success', 'Vehículo catálogo actualizado exitosamente.');
    }

    public function destroy(Vehiculo $Vehiculo)
    {
        try {
            // Eliminar la imagen si existe
            if ($Vehiculo->fotografia) {
                Storage::disk('public')->delete($Vehiculo->fotografia);
            }
            $Vehiculo->delete();
            return redirect()->route('admin.productos-servicios.vehiculos.index')
                            ->with('success', 'Vehículo catálogo eliminado exitosamente.');
        } catch (\Exception $e) {
            return redirect()->route('admin.productos-servicios.vehiculos.index')
                            ->with('error', 'No se pudo eliminar el vehículo catálogo.');
        }
    }
}