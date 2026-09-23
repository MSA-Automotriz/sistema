<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Marca;
use App\Models\Modelo;
use App\Models\Version;
use App\Models\AnioModelo;
use App\Models\Color;
use Illuminate\Http\Request;

class DependenciasController extends Controller
{
    /**
     * Obtener marcas por categoría de vehículo
     */
    public function marcasPorCategoria($categoria)
    {
        // En un caso real, tendrías una relación entre marcas y categorías de vehículos
        // Por ahora, vamos a simular una consulta filtrada
        
        $query = Marca::query();
        
        // Filtrar según la categoría (esto es solo un ejemplo, ajústalo según tu estructura de datos)
        if ($categoria === 'menores') {
            // Ejemplo: Marcas de motocicletas y vehículos menores
            $query->whereIn('id', [1, 2, 3]); // IDs de marcas como Honda, Yamaha, Suzuki, etc.
        } elseif ($categoria === 'livianos') {
            // Ejemplo: Marcas de vehículos livianos
            $query->whereIn('id', [4, 5, 6, 7]); // IDs de marcas como Toyota, Nissan, Hyundai, Kia, etc.
        } elseif ($categoria === 'pesados') {
            // Ejemplo: Marcas de vehículos pesados
            $query->whereIn('id', [8, 9, 10]); // IDs de marcas como Volvo, Scania, Mercedes-Benz, etc.
        }
        
        $marcas = $query->orderBy('nombre')->get(['id', 'nombre']);
        
        return response()->json($marcas);
    }
    
    /**
     * Obtener modelos por marca
     */
    public function modelosPorMarca($marcaId)
    {
        $modelos = Modelo::where('marca_id', $marcaId)
            ->orderBy('nombre')
            ->get(['id', 'nombre']);
        
        return response()->json($modelos);
    }
    
    /**
     * Obtener versiones por modelo
     */
    public function versionesPorModelo($modeloId)
    {
        $versiones = Version::where('modelo_id', $modeloId)
            ->orderBy('nombre')
            ->get(['id', 'nombre', 'cilindrada', 'transmision', 'traccion']);
        
        return response()->json($versiones);
    }
    
    /**
     * Obtener años modelo por versión
     */
    public function aniosPorVersion($versionId)
    {
        $anios = AnioModelo::where('version_id', $versionId)
            ->orderBy('anio', 'desc')
            ->get(['id', 'anio', 'precio', 'moneda']);
        
        return response()->json($anios);
    }
}