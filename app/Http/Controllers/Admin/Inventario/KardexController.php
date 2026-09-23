<?php

namespace App\Http\Controllers\Admin\Inventario;

use App\Http\Controllers\Controller;
use App\Models\Parte;
use App\Models\Vehiculo;
use App\Models\Almacen;
use App\Models\Kardex;
use Illuminate\Http\Request;

class KardexController extends Controller
{
    public function index()
    {
        $partes = Parte::all();
        $vehiculos = Vehiculo::with(['marca', 'modelo', 'version', 'anioModelo'])->get();
        $almacenes = Almacen::all();
        
        return view('admin.inventario.kardex.index', compact('partes', 'vehiculos', 'almacenes'));
    }
    
    public function reporte(Request $request)
    {
        // Tu lógica actual para generar reportes
        return view('admin.inventario.kardex.reporte', $data);
    }
    
    public function consulta(Request $request)
    {
        // Cargar almacenes
        $almacenes = Almacen::select('id', 'nombre')->orderBy('nombre')->get();

        // Cargar inventarios con relaciones necesarias
        $inventarios = \App\Models\Inventario::with([
            'parte' => fn($q) => $q->select('id', 'nombre', 'codigo'),
            'vehiculo.marca' => fn($q) => $q->select('id', 'nombre'),
            'vehiculo.modelo' => fn($q) => $q->select('id', 'nombre')
        ])->get();

        // Construir query base
        $query = Kardex::with([
            'parte' => fn($q) => $q->select('id', 'nombre', 'codigo'),
            'vehiculo' => ['marca', 'modelo'],
            'almacen' => fn($q) => $q->select('id', 'nombre'),
            'usuario' => fn($q) => $q->select('id', 'name')
        ])->orderBy('fecha_movimiento', 'desc');

        // Aplicar filtros
        if ($request->filled('inventario_id')) {
            $inventario = \App\Models\Inventario::findOrFail($request->inventario_id);
            $query->where('parte_id', $inventario->parte_id)
                ->where('almacen_id', $inventario->almacen_id)
                ->when($inventario->centro_costo_id, fn($q) => 
                    $q->where('centro_costo_id', $inventario->centro_costo_id)
                );
        }

        if ($request->filled('almacen_id')) {
            $query->where('almacen_id', $request->almacen_id);
        }

        if ($request->filled('tipo_item')) {
            if ($request->tipo_item === 'parte') {
                $query->whereNotNull('parte_id');
            } else {
                $query->whereNotNull('vehiculo_id');
            }
        }

        if ($request->filled('fecha_desde')) {
            $query->whereDate('fecha_movimiento', '>=', $request->fecha_desde);
        }

        if ($request->filled('fecha_hasta')) {
            $query->whereDate('fecha_movimiento', '<=', $request->fecha_hasta);
        }

        $movimientos = $query->paginate(50)->appends($request->query());

        // Manejar caso de inventarios vacíos
        if ($inventarios->isEmpty()) {
            \Illuminate\Support\Facades\Session::flash('warning', 'No hay inventarios registrados en el sistema.');
        }

        return view('admin.inventario.kardex.consulta', compact('movimientos', 'almacenes', 'inventarios'));
    }
}