<?php

namespace App\Observers;

use App\Models\DetalleOrdenCompra;
use App\Models\Movimiento;
use App\Services\KardexService;

class DetalleOrdenCompraObserver
{
    protected $kardexService;
    
    public function __construct(KardexService $kardexService)
    {
        $this->kardexService = $kardexService;
    }
    
    public function created(DetalleOrdenCompra $detalle)
    {
        if ($detalle->tipo_item === 'parte' && $detalle->ordenCompra->estado === 'RECIBIDA') {
            $this->kardexService->registrarMovimiento([
                'tipo_movimiento_id' => 1, // Entrada por compra
                'parte_id' => $detalle->item_id,
                'almacen_id' => $detalle->ordenCompra->almacen_destino_id,
                'cantidad' => $detalle->cantidad_en_compra,
                'afecta_stock' => 1, // Entrada
                'documento_tipo' => 'orden_compra',
                'documento_id' => $detalle->orden_compra_id,
                'documento_referencia' => $detalle->ordenCompra->codigo,
                'usuario_id' => $detalle->ordenCompra->aprobado_por,
                'costo_unitario' => $detalle->precio_compra
            ]);
        }
    }
}