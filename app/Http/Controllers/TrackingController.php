<?php

namespace App\Http\Controllers;

use App\Models\Venta;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class TrackingController extends Controller
{
    /**
     * Mostrar formulario de tracking
     */
    public function index()
    {
        return view('tracking.index');
    }

    /**
     * Buscar venta por código
     */
    public function buscarVenta(Request $request)
    {
        $request->validate([
            'codigo' => 'required|string',
            'documento' => 'required|string'
        ]);

        try {
            $venta = Venta::with(['cliente', 'detalles.parte', 'requerimientosCompra.estado'])
                ->where('codigo', $request->codigo)
                ->whereHas('cliente', function($query) use ($request) {
                    $query->where('documento_identidad', $request->documento);
                })
                ->first();

            if (!$venta) {
                return response()->json([
                    'success' => false,
                    'message' => 'No se encontró ninguna venta con los datos proporcionados'
                ], 404);
            }

            // Formatear datos para el tracking
            $trackingData = [
                'id' => $venta->id,
                'codigo' => $venta->codigo,
                'fecha' => $venta->fecha->format('d/m/Y'),
                'cliente' => $venta->cliente->razon_social ??
                    ($venta->cliente->nombres . ' ' . $venta->cliente->apellido_paterno),
                'total' => $venta->total,
                'moneda' => $venta->moneda,
                'estado' => $venta->estado,
                'estado_descripcion' => $this->getEstadoDescripcion($venta->estado),
                'progreso' => $this->calcularProgreso($venta->estado),
                'saldo_pendiente' => $venta->saldo_pendiente,
                'monto_abonado' => $venta->monto_abonado,
                'observaciones' => $venta->observaciones,
                'fecha_estimada_entrega' => $this->calcularFechaEstimadaEntrega($venta),
                'items' => $venta->detalles->map(function($detalle) {
                    return [
                        'nombre' => $detalle->parte->nombre ?? 'Producto',
                        'cantidad' => $detalle->cantidad,
                        'precio_unitario' => $detalle->precio_unitario,
                        'subtotal' => $detalle->subtotal
                    ];
                }),
                'requerimientos_compra' => $venta->requerimientosCompra->map(function($req) {
                    return [
                        'id' => $req->id,
                        'estado' => $req->estado->nombre ?? 'Pendiente',
                        'comentario' => $req->comentario,
                        'fecha_creacion' => $req->created_at->format('d/m/Y')
                    ];
                }),
                'historial_estados' => $this->formatearHistorialEstados($venta->detalle_estados ?? [])
            ];

            return response()->json([
                'success' => true,
                'venta' => $trackingData
            ]);

        } catch (\Exception $e) {
            Log::error('Error en buscarVenta tracking: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Error interno al buscar la venta'
            ], 500);
        }
    }

    /**
     * Obtener descripción del estado
     */
    private function getEstadoDescripcion($estado)
    {
        $descripciones = [
            'pendiente' => 'Su pedido está siendo procesado',
            'pagado' => 'Pago completado',
            'no_pagado' => 'Pendiente de pago',
            'pendiente_stock' => 'Esperando productos en stock',
            'en_compra' => 'Comprando productos para su pedido',
            'listo_entrega' => 'Su pedido está listo para entrega',
            'despachado' => 'Pedido entregado',
            'para_importacion' => 'Productos en proceso de importación',
            'pedido_especial' => 'Pedido especial en proceso',
            'cancelado' => 'Pedido cancelado'
        ];

        return $descripciones[$estado] ?? 'Estado desconocido';
    }

    /**
     * Calcular progreso como porcentaje
     */
    private function calcularProgreso($estado)
    {
        $progresos = [
            'pendiente' => 10,
            'no_pagado' => 20,
            'pagado' => 30,
            'pendiente_stock' => 40,
            'en_compra' => 60,
            'listo_entrega' => 80,
            'despachado' => 100,
            'para_importacion' => 50,
            'pedido_especial' => 50,
            'cancelado' => 0
        ];

        return $progresos[$estado] ?? 0;
    }

    /**
     * Calcular fecha estimada de entrega
     */
    private function calcularFechaEstimadaEntrega($venta)
    {
        if ($venta->fecha_despacho) {
            return $venta->fecha_despacho->format('d/m/Y');
        }

        // Estimar basado en el estado
        $diasEstimados = match($venta->estado) {
            'pendiente', 'no_pagado' => 1,
            'pagado' => 2,
            'pendiente_stock', 'en_compra' => 15,
            'listo_entrega' => 1,
            'para_importacion' => 30,
            'pedido_especial' => 20,
            default => 7
        };

        return now()->addDays($diasEstimados)->format('d/m/Y');
    }

    /**
     * Formatear historial de estados
     */
    private function formatearHistorialEstados($detalleEstados)
    {
        if (empty($detalleEstados)) {
            return [];
        }

        return collect($detalleEstados)->map(function($detalle) {
            return [
                'fecha' => date('d/m/Y H:i', strtotime($detalle['fecha'])),
                'estado_anterior' => $detalle['estado_anterior'],
                'estado_nuevo' => $detalle['estado_nuevo'],
                'comentario' => $detalle['comentario'] ?? ''
            ];
        })->reverse()->values()->toArray();
    }
}