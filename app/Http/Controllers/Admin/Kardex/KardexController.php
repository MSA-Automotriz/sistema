<?php
namespace App\Http\Controllers\Admin\Kardex;

use App\Http\Controllers\Controller;
use App\Models\Parte;
use App\Models\Almacen;
use App\Models\Vehiculo;
use App\Services\KardexService;
use Illuminate\Http\Request;

class KardexController extends Controller
{
    protected $kardexService;
   
    public function __construct(KardexService $kardexService)
    {
        $this->kardexService = $kardexService;
    }
   
    public function index()
    {
        $partes = Parte::orderBy('nombre')->get();
        $almacenes = Almacen::orderBy('nombre')->get();
        
        // Agregar la consulta de vehículos
        $vehiculos = Vehiculo::with(['marca', 'modelo', 'version', 'anioModelo'])
                            ->orderBy('id')
                            ->get();
       
        return view('admin.almacenes.inventario.kardex', compact('partes', 'almacenes', 'vehiculos'));
    }
   
    public function reporte(Request $request)
    {
        // Agregar validación para el tipo de reporte de vehículos
        $rules = [
            'tipo_reporte' => 'required|in:parte,vehiculo',
            'almacen_id' => 'nullable|exists:almacenes,id',
            'fecha_inicio' => 'nullable|date',
            'fecha_fin' => 'nullable|date|after_or_equal:fecha_inicio',
        ];

        if ($request->tipo_reporte === 'parte') {
            $rules['parte_id'] = 'required|exists:partes,id';
        } else {
            $rules['vehiculo_id'] = 'required|exists:vehiculos,id';
        }

        $request->validate($rules);
       
        if ($request->tipo_reporte === 'parte') {
            // Lógica existente para partes
            $parte = Parte::with('unidad')->findOrFail($request->parte_id);
            $almacen = $request->almacen_id ? Almacen::findOrFail($request->almacen_id) : null;
           
            $movimientos = $this->kardexService->getKardexParte(
                $request->parte_id,
                $request->almacen_id,
                $request->fecha_inicio,
                $request->fecha_fin ?? now()
            );
           
            $stockActual = $this->kardexService->getStockParte($request->parte_id);
           
            return view('admin.inventario.kardex.reporte', compact(
                'parte', 'almacen', 'movimientos', 'stockActual'
            ));
        } else {
            // Lógica para vehículos (deberás implementar estos métodos en tu servicio)
            $vehiculo = Vehiculo::with(['marca', 'modelo', 'version', 'anioModelo'])
                              ->findOrFail($request->vehiculo_id);
            $almacen = $request->almacen_id ? Almacen::findOrFail($request->almacen_id) : null;
           
            $movimientos = $this->kardexService->getKardexVehiculo(
                $request->vehiculo_id,
                $request->almacen_id,
                $request->fecha_inicio,
                $request->fecha_fin ?? now()
            );
           
            $stockActual = $this->kardexService->getStockVehiculo($request->vehiculo_id);
           
            return view('admin.inventario.kardex.reporte-vehiculo', compact(
                'vehiculo', 'almacen', 'movimientos', 'stockActual'
            ));
        }
    }
}