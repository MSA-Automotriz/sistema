<?php

namespace App\Http\Controllers\Admin\Ventas;

use App\Http\Controllers\Controller;
use App\Models\Venta;
use App\Models\PagoVenta;
use App\Models\TipoCambio;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class PagoVentaController extends Controller
{
    /**
     * Mostrar los pagos de una venta específica
     */
    public function index(Venta $venta)
    {
        $pagos = $venta->pagos()->with(['usuario', 'validadoPor'])->orderBy('fecha_pago', 'desc')->get();
        
        return response()->json([
            'success' => true,
            'pagos' => $pagos,
            'venta' => [
                'id' => $venta->id,
                'codigo' => $venta->codigo,
                'total' => $venta->total,
                'moneda' => $venta->moneda,
                'monto_abonado' => $venta->monto_abonado,
                'saldo_pendiente' => $venta->saldo_pendiente,
                'estado' => $venta->estado
            ]
        ]);
    }

    /**
     * Registrar un nuevo pago
     */
    public function store(Request $request, Venta $venta)
    {
        $validator = Validator::make($request->all(), [
            'monto' => 'required|numeric|min:0.01',
            'metodo_pago' => 'required|in:efectivo,transferencia,cheque,tarjeta_credito,tarjeta_debito,deposito,otro',
            'moneda' => 'required|in:PEN,USD',
            'fecha_pago' => 'required|date|before_or_equal:today',
            'referencia_pago' => 'nullable|string|max:255',
            'banco' => 'nullable|string|max:255',
            'observaciones' => 'nullable|string|max:500'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Datos inválidos',
                'errors' => $validator->errors()
            ], 422);
        }

        // Verificar que la venta permita pagos
        if ($venta->estado === 'pagado') {
            return response()->json([
                'success' => false,
                'message' => 'Esta venta ya está completamente pagada'
            ], 400);
        }

        if ($venta->saldo_pendiente <= 0) {
            return response()->json([
                'success' => false,
                'message' => 'No hay saldo pendiente por pagar'
            ], 400);
        }

        DB::beginTransaction();
        try {
            $monto = $request->monto;
            $monedaPago = $request->moneda;
            $monedaVenta = $venta->moneda === 'Dólares' ? 'USD' : 'PEN';
            
            // Obtener tipo de cambio si es necesario
            $tipoCambio = null;
            $montoConvertido = $monto;
            
            if ($monedaPago !== $monedaVenta) {
                $tipoCambioActual = TipoCambio::obtenerActual();
                if (!$tipoCambioActual) {
                    return response()->json([
                        'success' => false,
                        'message' => 'No hay tipo de cambio disponible para conversión de moneda'
                    ], 400);
                }
                
                $tipoCambio = $tipoCambioActual->venta;
                
                // Convertir monto
                if ($monedaPago === 'USD' && $monedaVenta === 'PEN') {
                    $montoConvertido = $monto * $tipoCambio;
                } elseif ($monedaPago === 'PEN' && $monedaVenta === 'USD') {
                    $montoConvertido = $monto / $tipoCambio;
                }
            }

            // Verificar que el monto no exceda el saldo pendiente
            if ($montoConvertido > $venta->saldo_pendiente) {
                return response()->json([
                    'success' => false,
                    'message' => 'El monto excede el saldo pendiente de ' . 
                                ($monedaVenta === 'USD' ? 'US$' : 'S/') . ' ' . 
                                number_format($venta->saldo_pendiente, 2)
                ], 400);
            }

            // Crear el pago
            $pago = PagoVenta::create([
                'venta_id' => $venta->id,
                'numero_pago' => PagoVenta::generarNumeroPago(),
                'fecha_pago' => $request->fecha_pago,
                'monto' => $monto,
                'moneda' => $monedaPago,
                'tipo_cambio' => $tipoCambio,
                'monto_convertido' => $montoConvertido,
                'metodo_pago' => $request->metodo_pago,
                'referencia_pago' => $request->referencia_pago,
                'banco' => $request->banco,
                'observaciones' => $request->observaciones,
                'usuario_id' => Auth::id()
            ]);

            // Actualizar la venta
            $venta->monto_abonado += $montoConvertido;
            $venta->saldo_pendiente -= $montoConvertido;

            // Cambiar estado según corresponda
            if ($venta->saldo_pendiente <= 0.01) { // Considerar diferencias mínimas por redondeo
                $venta->saldo_pendiente = 0;
                $venta->cambiarEstado('pagado', 'Pago completado - ' . $pago->numero_pago);
            } else {
                $venta->cambiarEstado('no_pagado', 'Pago parcial - ' . $pago->numero_pago);
            }

            $venta->save();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Pago registrado exitosamente',
                'pago' => $pago->load(['usuario']),
                'venta' => [
                    'monto_abonado' => $venta->monto_abonado,
                    'saldo_pendiente' => $venta->saldo_pendiente,
                    'estado' => $venta->estado
                ]
            ]);

        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'success' => false,
                'message' => 'Error al registrar el pago: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Validar un pago
     */
    public function validar(Request $request, Venta $venta, PagoVenta $pago)
    {
        if ($pago->venta_id !== $venta->id) {
            return response()->json([
                'success' => false,
                'message' => 'El pago no pertenece a esta venta'
            ], 400);
        }

        if ($pago->validado) {
            return response()->json([
                'success' => false,
                'message' => 'Este pago ya está validado'
            ], 400);
        }

        $pago->validar($request->comentario);

        return response()->json([
            'success' => true,
            'message' => 'Pago validado exitosamente',
            'pago' => $pago->load(['validadoPor'])
        ]);
    }

    /**
     * Eliminar un pago (solo si no está validado)
     */
    public function destroy(Venta $venta, PagoVenta $pago)
    {
        if ($pago->venta_id !== $venta->id) {
            return response()->json([
                'success' => false,
                'message' => 'El pago no pertenece a esta venta'
            ], 400);
        }

        if ($pago->validado) {
            return response()->json([
                'success' => false,
                'message' => 'No se puede eliminar un pago validado'
            ], 400);
        }

        DB::beginTransaction();
        try {
            // Revertir el monto en la venta
            $venta->monto_abonado -= $pago->monto_convertido;
            $venta->saldo_pendiente += $pago->monto_convertido;

            // Actualizar estado
            if ($venta->saldo_pendiente > 0) {
                $venta->cambiarEstado('no_pagado', 'Pago eliminado - ' . $pago->numero_pago);
            }

            $venta->save();
            $pago->delete();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Pago eliminado exitosamente',
                'venta' => [
                    'monto_abonado' => $venta->monto_abonado,
                    'saldo_pendiente' => $venta->saldo_pendiente,
                    'estado' => $venta->estado
                ]
            ]);

        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'success' => false,
                'message' => 'Error al eliminar el pago: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Reporte de cuentas por cobrar
     */
    public function cuentasPorCobrar(Request $request)
    {
        $query = Venta::with(['cliente', 'usuario', 'almacen'])
                     ->cuentasPorCobrar();

        // Filtros
        if ($request->filled('cliente_id')) {
            $query->where('cliente_id', $request->cliente_id);
        }

        if ($request->filled('estado')) {
            $query->where('estado', $request->estado);
        }

        if ($request->filled('vencidas')) {
            if ($request->vencidas === '1') {
                $query->vencidas();
            } elseif ($request->vencidas === '0') {
                $query->proximasVencer();
            }
        }

        if ($request->filled('fecha_desde')) {
            $query->whereDate('fecha', '>=', $request->fecha_desde);
        }

        if ($request->filled('fecha_hasta')) {
            $query->whereDate('fecha', '<=', $request->fecha_hasta);
        }

        $cuentasPorCobrar = $query->orderBy('fecha_vencimiento', 'asc')
                                 ->orderBy('fecha', 'desc')
                                 ->paginate(20);

        // Calcular totales
        $totalPendiente = $query->sum('saldo_pendiente');
        $totalVencido = Venta::cuentasPorCobrar()->vencidas()->sum('saldo_pendiente');

        return response()->json([
            'success' => true,
            'data' => $cuentasPorCobrar,
            'resumen' => [
                'total_pendiente' => $totalPendiente,
                'total_vencido' => $totalVencido,
                'cantidad_ventas' => $cuentasPorCobrar->total()
            ]
        ]);
    }
}