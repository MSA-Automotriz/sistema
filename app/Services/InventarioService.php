<?php

namespace App\Services;

use App\Models\Inventario;
use App\Models\Kardex;
use App\Models\DetalleOrdenCompra;
use App\Models\RecepcionOrdenCompra;
use App\Models\DevolucionOrdenCompra;
use Illuminate\Support\Facades\DB;

class InventarioService
{
    /**
     * Procesar recepción de items y actualizar inventario
     */
    public function procesarRecepcion(RecepcionOrdenCompra $recepcion)
    {
        DB::transaction(function () use ($recepcion) {
            $detalle = $recepcion->detalleOrdenCompra;
            $orden = $detalle->ordenCompra;
            
            // Determinar si es parte o vehículo
            $esVehiculo = $detalle->tipo_item === 'vehiculo';
            $itemId = $detalle->item_id;
            $almacenId = $orden->almacen_destino_id;
            
            // Buscar o crear registro de inventario
            $inventario = $this->buscarOCrearInventario($itemId, $almacenId, $esVehiculo);
            
            // Obtener stock anterior
            $stockAnterior = $inventario->stock_disponible;
            
            // Calcular nuevo stock
            $nuevoStock = $stockAnterior + $recepcion->cantidad_recibida;
            
            // Actualizar inventario
            $inventario->update([
                'stock_disponible' => $nuevoStock
            ]);
            
            // Registrar en kardex
            $this->registrarMovimientoKardex([
                'parte_id' => $esVehiculo ? null : $itemId,
                'vehiculo_id' => $esVehiculo ? $itemId : null,
                'almacen_id' => $almacenId,
                'cantidad' => $recepcion->cantidad_recibida,
                'stock_anterior' => $stockAnterior,
                'stock_actual' => $nuevoStock,
                'costo_unitario' => $detalle->precio_compra,
                'numero_documento' => $orden->codigo,
                'fecha_movimiento' => $recepcion->fecha_recepcion,
                'usuario_id' => $recepcion->recibido_por,
                'referencia_id' => $recepcion->id,
                'referencia_tipo' => 'App\Models\RecepcionOrdenCompra',
                'observaciones' => $recepcion->observaciones,
                'tipo_operacion' => 'entrada_compra'
            ]);
        });
    }
    
    /**
     * Procesar devolución de items y actualizar inventario
     */
    public function procesarDevolucion(DevolucionOrdenCompra $devolucion)
    {
        DB::transaction(function () use ($devolucion) {
            $detalle = $devolucion->detalleOrdenCompra;
            $orden = $detalle->ordenCompra;
            
            // Determinar si es parte o vehículo
            $esVehiculo = $detalle->tipo_item === 'vehiculo';
            $itemId = $detalle->item_id;
            $almacenId = $orden->almacen_destino_id;
            
            // Buscar inventario
            $inventario = $this->buscarInventario($itemId, $almacenId, $esVehiculo);
            
            if (!$inventario) {
                throw new \Exception('No se encontró inventario para el item');
            }
            
            // Verificar que hay suficiente stock
            if ($inventario->stock_disponible < $devolucion->cantidad_devuelta) {
                throw new \Exception('Stock insuficiente para realizar la devolución');
            }
            
            // Obtener stock anterior
            $stockAnterior = $inventario->stock_disponible;
            
            // Calcular nuevo stock
            $nuevoStock = $stockAnterior - $devolucion->cantidad_devuelta;
            
            // Actualizar inventario
            $inventario->update([
                'stock_disponible' => $nuevoStock
            ]);
            
            // Registrar en kardex
            $this->registrarMovimientoKardex([
                'parte_id' => $esVehiculo ? null : $itemId,
                'vehiculo_id' => $esVehiculo ? $itemId : null,
                'almacen_id' => $almacenId,
                'cantidad' => $devolucion->cantidad_devuelta,
                'stock_anterior' => $stockAnterior,
                'stock_actual' => $nuevoStock,
                'costo_unitario' => $detalle->precio_compra,
                'numero_documento' => $orden->codigo,
                'fecha_movimiento' => $devolucion->fecha_devolucion,
                'usuario_id' => $devolucion->devuelto_por,
                'referencia_id' => $devolucion->id,
                'referencia_tipo' => 'App\Models\DevolucionOrdenCompra',
                'observaciones' => $devolucion->motivo,
                'tipo_operacion' => 'devolucion_compra'
            ]);
        });
    }
    
    /**
     * Buscar o crear registro de inventario
     */
    private function buscarOCrearInventario($itemId, $almacenId, $esVehiculo = false)
    {
        $query = Inventario::where('almacen_id', $almacenId);
        
        if ($esVehiculo) {
            $query->where('vehiculo_id', $itemId)->whereNull('parte_id');
        } else {
            $query->where('parte_id', $itemId)->whereNull('vehiculo_id');
        }
        
        $inventario = $query->first();
        
        if (!$inventario) {
            $inventario = Inventario::create([
                'parte_id' => $esVehiculo ? null : $itemId,
                'vehiculo_id' => $esVehiculo ? $itemId : null,
                'almacen_id' => $almacenId,
                'stock_disponible' => 0,
                'stock_reservado' => 0,
                'stock_minimo' => 0,
                'stock_maximo' => 0
            ]);
        }
        
        return $inventario;
    }
    
    /**
     * Buscar registro de inventario existente
     */
    private function buscarInventario($itemId, $almacenId, $esVehiculo = false)
    {
        $query = Inventario::where('almacen_id', $almacenId);
        
        if ($esVehiculo) {
            $query->where('vehiculo_id', $itemId)->whereNull('parte_id');
        } else {
            $query->where('parte_id', $itemId)->whereNull('vehiculo_id');
        }
        
        return $query->first();
    }
    
    /**
     * Registrar movimiento en kardex
     */
    private function registrarMovimientoKardex($data)
    {
        $tipoOperacion = $data['tipo_operacion'];
        
        if ($tipoOperacion === 'entrada_compra') {
            return Kardex::registrarEntradaCompra([
                'parte_id' => $data['parte_id'],
                'vehiculo_id' => $data['vehiculo_id'],
                'almacen_id' => $data['almacen_id'],
                'cantidad' => $data['cantidad'],
                'stock_anterior' => $data['stock_anterior'],
                'stock_actual' => $data['stock_actual'],
                'costo_unitario' => $data['costo_unitario'],
                'numero_documento' => $data['numero_documento'],
                'fecha_movimiento' => $data['fecha_movimiento'],
                'usuario_id' => $data['usuario_id'],
                'referencia_id' => $data['referencia_id'],
                'referencia_tipo' => $data['referencia_tipo'],
                'observaciones' => $data['observaciones']
            ]);
        } elseif ($tipoOperacion === 'devolucion_compra') {
            return Kardex::registrarDevolucionCompra([
                'parte_id' => $data['parte_id'],
                'vehiculo_id' => $data['vehiculo_id'],
                'almacen_id' => $data['almacen_id'],
                'cantidad' => $data['cantidad'],
                'stock_anterior' => $data['stock_anterior'],
                'stock_actual' => $data['stock_actual'],
                'costo_unitario' => $data['costo_unitario'],
                'numero_documento' => $data['numero_documento'],
                'fecha_movimiento' => $data['fecha_movimiento'],
                'usuario_id' => $data['usuario_id'],
                'referencia_id' => $data['referencia_id'],
                'referencia_tipo' => $data['referencia_tipo'],
                'observaciones' => $data['observaciones']
            ]);
        }
    }
    
    /**
     * Obtener stock actual de un item en un almacén
     */
    public function obtenerStock($itemId, $almacenId, $esVehiculo = false)
    {
        $inventario = $this->buscarInventario($itemId, $almacenId, $esVehiculo);
        
        return $inventario ? $inventario->stock_disponible : 0;
    }
    
    /**
     * Verificar si hay stock suficiente
     */
    public function verificarStock($itemId, $almacenId, $cantidadRequerida, $esVehiculo = false)
    {
        $stockActual = $this->obtenerStock($itemId, $almacenId, $esVehiculo);
        
        return $stockActual >= $cantidadRequerida;
    }
}