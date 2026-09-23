<?php
// app/Models/Venta.php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Venta extends Model
{
    use HasFactory;
    protected $table = 'ventas';
   
    protected $fillable = [
        'codigo',
        'numero_factura',
        'fecha',
        'fecha_vencimiento',
        'fecha_despacho',
        'cliente_id',
        'usuario_id',
        'almacen_id',
        'subtotal',
        'igv',
        'total',
        'moneda',
        'tipo_pago',
        'tipo_cambio_usado',
        'estado',
        'prioridad',
        'requiere_importacion',
        'observaciones',
        'notas_internas',
        'detalle_estados',
        'cotizacion_id',
        'monto_abonado',
        'saldo_pendiente'
    ];
   
    protected $casts = [
        'fecha' => 'datetime',
        'fecha_vencimiento' => 'date',
        'fecha_despacho' => 'date',
        'subtotal' => 'decimal:2',
        'igv' => 'decimal:2',
        'total' => 'decimal:2',
        'tipo_cambio_usado' => 'decimal:4',
        'monto_abonado' => 'decimal:2',
        'saldo_pendiente' => 'decimal:2',
        'requiere_importacion' => 'boolean',
        'detalle_estados' => 'array'
    ];
   
    // Relaciones
    public function cliente()
    {
        return $this->belongsTo(Cliente::class);
    }
   
    public function usuario()
    {
        return $this->belongsTo(User::class, 'usuario_id');
    }
   
    public function almacen()
    {
        return $this->belongsTo(Almacen::class);
    }
   
    public function detalles()
    {
        return $this->hasMany(DetalleVenta::class);
    }
   
    public function cotizacion()
    {
        return $this->belongsTo(Cotizacion::class);
    }
    
    // Nueva relación con requerimientos de compra
    public function requerimientosCompra()
    {
        return $this->hasMany(RequerimientoCompra::class, 'venta_id');
    }
    
    // Relación con pagos
    public function pagos()
    {
        return $this->hasMany(PagoVenta::class);
    }
   
    // Generador de código automático
    public static function generarCodigo()
    {
        $anio = date('Y');
        $mes = date('m');
        $ultimaVenta = self::whereYear('created_at', $anio)
            ->whereMonth('created_at', $mes)
            ->orderBy('id', 'desc')
            ->first();
       
        $numero = $ultimaVenta ? intval(substr($ultimaVenta->codigo, -6)) + 1 : 1;
        return 'VTA-' . $anio . $mes . str_pad($numero, 6, '0', STR_PAD_LEFT);
    }
    
    /**
     * Verifica si la venta está pagada completamente
     */
    public function estaPagada()
    {
        return $this->saldo_pendiente <= 0;
    }
    
    /**
     * Obtiene el porcentaje abonado
     */
    public function getPorcentajeAbonadoAttribute()
    {
        if ($this->total > 0) {
            return round(($this->monto_abonado / $this->total) * 100, 2);
        }
        return 0;
    }
    
    
    /**
     * Genera un requerimiento de compra para items sin stock
     */
    public function generarRequerimientoCompra($itemsSinStock, $estadoId, $comentario = null)
    {
        if (empty($itemsSinStock)) {
            return null;
        }
        
        // Generar código automático para el requerimiento
        $ultimoRequerimiento = RequerimientoCompra::latest('id')->first();
        $numeroRequerimiento = $ultimoRequerimiento ? $ultimoRequerimiento->id + 1 : 1;
        $codigo = 'REQ-' . str_pad($numeroRequerimiento, 6, '0', STR_PAD_LEFT);

        $requerimiento = new RequerimientoCompra();
        $requerimiento->codigo = $codigo;
        $requerimiento->tipo = 'inventario';
        $requerimiento->almacen_id = $this->almacen_id;
        $requerimiento->fecha = now()->toDateString();
        $requerimiento->comentario = $comentario ?? "Requerimiento generado automáticamente desde POS. Venta: {$this->codigo}";
        $requerimiento->estado_id = $estadoId;
        $requerimiento->cotizacion_id = $this->cotizacion_id;
        $requerimiento->venta_id = $this->id;
        $requerimiento->prioridad = 'Alta';
        $requerimiento->user_id = $this->usuario_id;
        $requerimiento->save();
        
        // Crear detalles del requerimiento
        foreach ($itemsSinStock as $item) {
            if ($item['tipo'] == 'parte') {
                $detalle = new DetalleRequerimientoCompra();
                $detalle->requerimiento_compra_id = $requerimiento->id;
                $detalle->item_id = $item['id'];
                $detalle->tipo_item = 'parte';
                $detalle->cantidad = $item['cantidad'];
                $detalle->descripcion = "Requerido para venta {$this->codigo}";
                $detalle->save();
            }
        }
        
        return $requerimiento;
    }

    public function detallesPOS()
    {
        return $this->hasMany(DetalleVentaPOS::class, 'venta_id');
    }

    /**
     * Verificar stock y gestionar flujo de compra automático
     */
    public function verificarYGestionarStock()
    {
        $itemsSinStock = [];
        $requiereCompra = false;

        // Verificar stock de los items de la venta
        foreach ($this->detalles as $detalle) {
            if ($detalle->tipo_item === 'parte') {
                $parte = $detalle->parte;
                if ($parte && $parte->stock_actual < $detalle->cantidad) {
                    $itemsSinStock[] = [
                        'id' => $parte->id,
                        'tipo' => 'parte',
                        'nombre' => $parte->nombre,
                        'cantidad' => $detalle->cantidad,
                        'stock_actual' => $parte->stock_actual,
                        'cantidad_faltante' => $detalle->cantidad - $parte->stock_actual
                    ];
                    $requiereCompra = true;
                }
            }
        }

        if ($requiereCompra) {
            // Cambiar estado a pendiente de stock
            $this->cambiarEstado('pendiente_stock', 'Venta requiere compra de productos sin stock');

            // Buscar estado "pendiente" para requerimientos o crear uno por defecto
            $estadoPendiente = \App\Models\Estado::where('nombre', 'Pendiente')->first();

            // Si no existe, crear un estado por defecto o usar ID directo
            if (!$estadoPendiente) {
                // Usar un ID por defecto (1) o crear el estado
                $estadoId = 1; // Asumiendo que existe un estado con ID 1
                \Log::info('🔍 STOCK: No se encontró estado Pendiente, usando ID por defecto: ' . $estadoId);
            } else {
                $estadoId = $estadoPendiente->id;
            }

            // Generar requerimiento de compra automáticamente
            $requerimiento = $this->generarRequerimientoCompra(
                $itemsSinStock,
                $estadoId,
                "Requerimiento automático para venta {$this->codigo}. Items sin stock: " .
                implode(', ', array_column($itemsSinStock, 'nombre'))
            );

            if ($requerimiento) {
                // Cambiar a estado "en compra"
                $this->cambiarEstado('en_compra', "Requerimiento de compra #{$requerimiento->id} generado automáticamente");

                return [
                    'requiere_compra' => true,
                    'items_sin_stock' => $itemsSinStock,
                    'requerimiento_id' => $requerimiento->id,
                    'mensaje' => "Se generó requerimiento de compra #{$requerimiento->id} para productos sin stock"
                ];
            }
        }

        // Si no requiere compra y está completamente pagado, cambiar a pagado
        if ($this->saldo_pendiente <= 0) {
            $this->cambiarEstado('pagado', 'Venta completada - todos los productos tienen stock suficiente');
        }

        return [
            'requiere_compra' => false,
            'items_sin_stock' => [],
            'mensaje' => 'Todos los productos tienen stock suficiente'
        ];
    }
    
    // ========== MÉTODOS PARA GESTIÓN DE ESTADOS ==========
    
    /**
     * Estados disponibles para las ventas
     */
    public static function getEstadosDisponibles()
    {
        return [
            'pendiente' => 'Pendiente',
            'pagado' => 'Pagado',
            'no_pagado' => 'No Pagado',
            'en_cotizacion' => 'En Cotización',
            'pendiente_stock' => 'Pendiente de Stock',
            'en_compra' => 'En Proceso de Compra',
            'listo_entrega' => 'Listo para Entrega',
            'despachado' => 'Despachado',
            'para_importacion' => 'Para Importación',
            'pedido_especial' => 'Pedido Especial',
            'cancelado' => 'Cancelado'
        ];
    }
    
    /**
     * Cambiar estado de la venta con tracking
     */
    public function cambiarEstado($nuevoEstado, $comentario = null, $usuarioId = null)
    {
        $estadoAnterior = $this->estado;
        
        // Validar que el estado existe
        if (!array_key_exists($nuevoEstado, self::getEstadosDisponibles())) {
            return false;
        }
        
        // Actualizar estado
        $this->estado = $nuevoEstado;
        
        // Tracking de cambios de estado
        $detalleEstados = $this->detalle_estados ?? [];
        $detalleEstados[] = [
            'fecha' => now()->toISOString(),
            'estado_anterior' => $estadoAnterior,
            'estado_nuevo' => $nuevoEstado,
            'usuario_id' => $usuarioId ?? auth()->id(),
            'comentario' => $comentario
        ];
        $this->detalle_estados = $detalleEstados;
        
        // Lógica especial según el estado
        switch ($nuevoEstado) {
            case 'pagado':
                $this->saldo_pendiente = 0;
                $this->monto_abonado = $this->total;
                break;

            case 'no_pagado':
                if ($this->tipo_pago === 'Crédito' && $this->saldo_pendiente <= 0) {
                    $this->saldo_pendiente = $this->total - $this->monto_abonado;
                }
                break;

            case 'pendiente_stock':
                // Marcar que la venta está esperando stock
                break;

            case 'en_compra':
                // Estado cuando se ha generado orden de compra
                break;

            case 'listo_entrega':
                // Estado cuando el stock llegó y está listo para entregar
                break;

            case 'despachado':
                if (!$this->fecha_despacho) {
                    $this->fecha_despacho = now()->toDateString();
                }
                break;
        }
        
        return $this->save();
    }
    
    /**
     * Verificar si está vencida (para crédito)
     */
    public function estaVencida()
    {
        return $this->fecha_vencimiento && 
               $this->fecha_vencimiento->isPast() && 
               $this->saldo_pendiente > 0;
    }
    
    /**
     * Días de vencimiento (negativo si está vencida)
     */
    public function diasVencimiento()
    {
        if (!$this->fecha_vencimiento) {
            return null;
        }
        
        return now()->diffInDays($this->fecha_vencimiento, false);
    }
    
    /**
     * Registrar un pago mejorado
     */
    public function registrarPago($monto, $metodo = 'efectivo', $referencia = null, $observaciones = null, $moneda = null)
    {
        if ($monto <= 0 || $monto > $this->saldo_pendiente) {
            return false;
        }

        // Logs de depuración
        \Log::info('🔍 PAGO: registrarPago iniciado', [
            'venta_id' => $this->id,
            'venta_moneda' => $this->moneda,
            'moneda_parametro' => $moneda,
            'monto' => $monto
        ]);

        // Mapear moneda correctamente (siempre aplicar mapeo)
        $monedaAMapear = $moneda ?? $this->moneda;

        switch ($monedaAMapear) {
            case 'Dólares':
            case 'USD':
                $monedaMapeada = 'USD';
                break;
            case 'Soles':
            case 'PEN':
            default:
                $monedaMapeada = 'PEN';
                break;
        }

        \Log::info('🔍 PAGO: Moneda mapeada', [
            'moneda_original' => $this->moneda,
            'moneda_parametro' => $moneda,
            'moneda_mapeada' => $monedaMapeada
        ]);

        // Crear el registro de pago
        $pago = new PagoVenta([
            'venta_id' => $this->id,
            'numero_pago' => PagoVenta::generarNumeroPago(),
            'fecha_pago' => now()->toDateString(),
            'monto' => $monto,
            'moneda' => $monedaMapeada,
            'metodo_pago' => $metodo,
            'referencia_pago' => $referencia,
            'observaciones' => $observaciones,
            'usuario_id' => auth()->id()
        ]);
        
        // Si la moneda del pago es diferente, calcular conversión
        $monedaVenta = $this->moneda === 'Dólares' ? 'USD' : 'PEN';
        $montoConvertido = $pago->calcularMontoConvertido($monedaVenta);
        
        $pago->save();
        
        // Actualizar la venta
        $this->monto_abonado += $montoConvertido;
        $this->saldo_pendiente -= $montoConvertido;
        
        // Cambiar estado si se pagó todo
        if ($this->saldo_pendiente <= 0) {
            $this->cambiarEstado('pagado', 'Pago completado');
            $this->saldo_pendiente = 0;
        } else {
            $this->cambiarEstado('no_pagado', 'Pago parcial registrado');
        }
        
        return $this->save();
    }
    
    /**
     * Marcar venta como lista para entrega (cuando llega el stock)
     */
    public function marcarListaParaEntrega($comentario = null, $usuarioId = null)
    {
        if (!in_array($this->estado, ['pendiente_stock', 'en_compra'])) {
            return false;
        }

        $comentarioFinal = $comentario ?? 'Stock recibido - venta lista para entrega';
        return $this->cambiarEstado('listo_entrega', $comentarioFinal, $usuarioId);
    }

    /**
     * Marcar venta como despachada (entregada al cliente)
     */
    public function marcarComoDespachada($comentario = null, $usuarioId = null)
    {
        if ($this->estado !== 'listo_entrega') {
            return false;
        }

        // Verificar que esté pagada completamente
        if ($this->saldo_pendiente > 0) {
            return [
                'success' => false,
                'message' => 'No se puede despachar una venta con saldo pendiente'
            ];
        }

        $comentarioFinal = $comentario ?? 'Venta entregada al cliente';
        $resultado = $this->cambiarEstado('despachado', $comentarioFinal, $usuarioId);

        if ($resultado) {
            // Actualizar fecha de despacho si no existe
            if (!$this->fecha_despacho) {
                $this->fecha_despacho = now()->toDateString();
                $this->save();
            }
        }

        return [
            'success' => $resultado,
            'message' => $resultado ? 'Venta marcada como despachada' : 'Error al marcar como despachada'
        ];
    }

    /**
     * Verificar si puede ser marcada como lista para entrega
     */
    public function puedeMarcarseListaEntrega()
    {
        return in_array($this->estado, ['pendiente_stock', 'en_compra']);
    }

    /**
     * Verificar si puede ser despachada
     */
    public function puedeDespacharse()
    {
        return $this->estado === 'listo_entrega' && $this->saldo_pendiente <= 0;
    }

    // ========== SCOPES ==========
    
    public function scopeEstado($query, $estado)
    {
        return $query->where('estado', $estado);
    }
    
    public function scopeVencidas($query)
    {
        return $query->where('fecha_vencimiento', '<', now())
                    ->where('saldo_pendiente', '>', 0);
    }
    
    public function scopeProximasVencer($query, $dias = 7)
    {
        return $query->whereBetween('fecha_vencimiento', [now(), now()->addDays($dias)])
                    ->where('saldo_pendiente', '>', 0);
    }
    
    public function scopeCuentasPorCobrar($query)
    {
        return $query->where('tipo_pago', 'Crédito')
                    ->where('saldo_pendiente', '>', 0);
    }
    
    public function scopePorPrioridad($query, $prioridad)
    {
        return $query->where('prioridad', $prioridad);
    }
}