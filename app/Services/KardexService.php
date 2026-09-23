<?php

namespace App\Services;

use App\Models\Movimiento;
use App\Models\Inventario;
use App\Models\Parte;
use App\Models\Almacen;
use Illuminate\Support\Facades\DB;

class KardexService
{
    /**
     * Registra un movimiento de inventario y actualiza el stock
     */
    public function registrarMovimiento($datos)
    {
        DB::beginTransaction();
        
        try {
            // Obtener el inventario actual
            $inventario = Inventario::where('parte_id', $datos['parte_id'])
                ->where('almacen_id', $datos['almacen_id'])
                ->first();
                
            if (!$inventario && $datos['afecta_stock'] > 0) {
                // Si no existe y es una entrada, crear el inventario
                $inventario = Inventario::create([
                    'parte_id' => $datos['parte_id'],
                    'almacen_id' => $datos['almacen_id'],
                    'centro_costo_id' => $datos['centro_costo_id'] ?? null,
                    'stock_disponible' => 0,
                    'stock_reservado' => 0,
                    'stock_minimo' => 0,
                    'stock_maximo' => 0,
                ]);
            } elseif (!$inventario) {
                throw new \Exception("No existe inventario para esta parte en este almacén");
            }
            
            $stockAnterior = $inventario->stock_disponible;
            $cantidad = $datos['cantidad'] * $datos['afecta_stock'];
            $stockResultante = $stockAnterior + $cantidad;
            
            // Actualizar inventario
            $inventario->stock_disponible = $stockResultante;
            $inventario->save();
            
            // Registrar movimiento
            $movimiento = Movimiento::create([
                'tipo_movimiento_id' => $datos['tipo_movimiento_id'],
                'parte_id' => $datos['parte_id'],
                'almacen_id' => $datos['almacen_id'],
                'centro_costo_id' => $datos['centro_costo_id'] ?? null,
                'cantidad' => $datos['cantidad'],
                'stock_anterior' => $stockAnterior,
                'stock_resultante' => $stockResultante,
                'documento_tipo' => $datos['documento_tipo'] ?? null,
                'documento_id' => $datos['documento_id'] ?? null,
                'documento_referencia' => $datos['documento_referencia'] ?? null,
                'fecha_movimiento' => $datos['fecha_movimiento'] ?? now(),
                'usuario_id' => $datos['usuario_id'],
                'observaciones' => $datos['observaciones'] ?? null,
                'costo_unitario' => $datos['costo_unitario'] ?? 0,
            ]);
            
            DB::commit();
            return $movimiento;
            
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
    
    /**
     * Obtiene el reporte Kardex de una parte específica
     */
    public function getKardexParte($parteId, $almacenId = null, $fechaInicio = null, $fechaFin = null)
    {
        $query = Movimiento::with(['tipoMovimiento', 'almacen', 'centroCosto', 'usuario'])
            ->where('parte_id', $parteId);
            
        if ($almacenId) {
            $query->where('almacen_id', $almacenId);
        }
        
        if ($fechaInicio) {
            $query->where('fecha_movimiento', '>=', $fechaInicio);
        }
        
        if ($fechaFin) {
            $query->where('fecha_movimiento', '<=', $fechaFin);
        }
        
        return $query->orderBy('fecha_movimiento', 'asc')
            ->get();
    }
    
    /**
     * Obtiene el stock actual de una parte en todos los almacenes
     */
    public function getStockParte($parteId)
    {
        return Inventario::with('almacen')
            ->where('parte_id', $parteId)
            ->get();
    }
}