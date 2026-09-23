<?php

namespace App\Http\Controllers\Admin\Mantenimiento;

use App\Http\Controllers\Controller;
use App\Models\Marca;
use App\Models\Modelo;
use App\Models\VehiculoMantenimiento;
use App\Models\Combustible;
use Illuminate\Http\Request;

class VehiculoMantenimientoController extends Controller
{
    /**
     * Buscar vehículo por placa
     */
    public function buscar(Request $request)
    {
        $placa = $request->get('placa');
        
        if (empty($placa)) {
            return response()->json([]);
        }
        
        $vehiculo = VehiculoMantenimiento::where('nro_placa', 'like', '%' . $placa . '%')
            ->with(['marca', 'modelo', 'combustible', 'cliente'])
            ->first();
            
        return response()->json($vehiculo);
    }
    
    /**
     * Guardar un nuevo vehículo desde el formulario AJAX
     */
    public function guardar(Request $request)
    {
        // Validación de datos
        $request->validate([
            'marca_id' => 'required|exists:marcas,id',
            'modelo_id' => 'required|exists:modelos,id',
            'anio' => 'required|integer|min:1900|max:' . (date('Y') + 1),
            'color' => 'required|string|max:50',
            'nro_placa' => 'required|string|max:20|unique:vehiculos_mantenimiento,nro_placa',
            'serie_vim' => 'required|string|max:50',
            'motor' => 'required|string|max:50',
            'combustible_id' => 'required|exists:combustibles,id',
            'kilometraje' => 'required|integer|min:0',
            'cliente_id' => 'required|exists:clientes,id',
        ]);
        
        try {
            // Crear vehículo
            $vehiculo = VehiculoMantenimiento::create([
                'marca_id' => $request->marca_id,
                'modelo_id' => $request->modelo_id,
                'anio' => $request->anio,
                'color' => $request->color,
                'nro_placa' => $request->nro_placa,
                'serie_vim' => $request->serie_vim,
                'motor' => $request->motor,
                'combustible_id' => $request->combustible_id,
                'kilometraje' => $request->kilometraje,
                'cliente_id' => $request->cliente_id,
            ]);
            
            // Cargar relaciones
            $vehiculo->load(['marca', 'modelo', 'combustible', 'cliente']);
            
            return response()->json([
                'success' => true,
                'vehiculo' => $vehiculo,
                'message' => 'Vehículo creado correctamente'
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al crear el vehículo: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Obtener lista de marcas para select
     */
    public function marcas()
    {
        $marcas = Marca::orderBy('nombre')->get();
        return response()->json($marcas);
    }
    
    /**
     * Obtener modelos por marca para select
     */
    public function modelos(Request $request)
    {
        $marca_id = $request->get('marca_id');
        
        if (empty($marca_id)) {
            return response()->json([]);
        }
        
        $modelos = Modelo::where('marca_id', $marca_id)
            ->orderBy('nombre')
            ->get();
            
        return response()->json($modelos);
    }
    
    /**
     * Obtener combustibles para select
     */
    public function combustibles()
    {
        $combustibles = Combustible::orderBy('nombre')->get();
        return response()->json($combustibles);
    }
}