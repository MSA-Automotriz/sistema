<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Kardex extends Model
{
    protected $table = 'kardex';
    
    protected $fillable = [
        'parte_id',
        'vehiculo_id',
        'almacen_id',
        'tipo_movimiento',
        'concepto',
        'numero_documento',
        'cantidad_entrada',
        'cantidad_salida',
        'stock_anterior',
        'stock_actual',
        'costo_unitario',
        'valor_total',
        'fecha_movimiento',
        'usuario_id',
        'referencia_id',
        'referencia_tipo',
        'observaciones'
    ];
    
    protected $casts = [
        'fecha_movimiento' => 'datetime',
        'cantidad_entrada' => 'decimal:2',
        'cantidad_salida' => 'decimal:2',
        'stock_anterior' => 'decimal:2',
        'stock_actual' => 'decimal:2',
        'costo_unitario' => 'decimal:4',
        'valor_total' => 'decimal:2'
    ];
    
    // Constantes para tipos de movimiento
    const TIPO_ENTRADA = 'ENTRADA';
    const TIPO_SALIDA = 'SALIDA';
    const TIPO_AJUSTE = 'AJUSTE';
    
    // Constantes para conceptos
    const CONCEPTO_COMPRA = 'COMPRA';
    const CONCEPTO_DEVOLUCION_COMPRA = 'DEVOLUCION_COMPRA';
    const CONCEPTO_VENTA = 'VENTA';
    const CONCEPTO_DEVOLUCION_VENTA = 'DEVOLUCION_VENTA';
    const CONCEPTO_AJUSTE_INVENTARIO = 'AJUSTE_INVENTARIO';
    const CONCEPTO_TRANSFERENCIA_ENTRADA = 'TRANSFERENCIA_ENTRADA';
    const CONCEPTO_TRANSFERENCIA_SALIDA = 'TRANSFERENCIA_SALIDA';
    
    // Relaciones
    public function parte()
    {
        return $this->belongsTo(Parte::class);
    }
    
    public function vehiculo()
    {
        return $this->belongsTo(Vehiculo::class);
    }
    
    public function almacen()
    {
        return $this->belongsTo(Almacen::class);
    }
    
    public function usuario()
    {
        return $this->belongsTo(User::class, 'usuario_id');
    }
    
    // Método para registrar movimiento de entrada por compra
    public static function registrarEntradaCompra($data)
    {
        return self::create([
            'parte_id' => $data['parte_id'] ?? null,
            'vehiculo_id' => $data['vehiculo_id'] ?? null,
            'almacen_id' => $data['almacen_id'],
            'tipo_movimiento' => self::TIPO_ENTRADA,
            'concepto' => self::CONCEPTO_COMPRA,
            'numero_documento' => $data['numero_documento'],
            'cantidad_entrada' => $data['cantidad'],
            'cantidad_salida' => 0,
            'stock_anterior' => $data['stock_anterior'],
            'stock_actual' => $data['stock_actual'],
            'costo_unitario' => $data['costo_unitario'],
            'valor_total' => $data['cantidad'] * $data['costo_unitario'],
            'fecha_movimiento' => $data['fecha_movimiento'],
            'usuario_id' => $data['usuario_id'],
            'referencia_id' => $data['referencia_id'],
            'referencia_tipo' => $data['referencia_tipo'],
            'observaciones' => $data['observaciones'] ?? null
        ]);
    }
    
    // Método para registrar devolución de compra
    public static function registrarDevolucionCompra($data)
    {
        return self::create([
            'parte_id' => $data['parte_id'] ?? null,
            'vehiculo_id' => $data['vehiculo_id'] ?? null,
            'almacen_id' => $data['almacen_id'],
            'tipo_movimiento' => self::TIPO_SALIDA,
            'concepto' => self::CONCEPTO_DEVOLUCION_COMPRA,
            'numero_documento' => $data['numero_documento'],
            'cantidad_entrada' => 0,
            'cantidad_salida' => $data['cantidad'],
            'stock_anterior' => $data['stock_anterior'],
            'stock_actual' => $data['stock_actual'],
            'costo_unitario' => $data['costo_unitario'],
            'valor_total' => $data['cantidad'] * $data['costo_unitario'],
            'fecha_movimiento' => $data['fecha_movimiento'],
            'usuario_id' => $data['usuario_id'],
            'referencia_id' => $data['referencia_id'],
            'referencia_tipo' => $data['referencia_tipo'],
            'observaciones' => $data['observaciones'] ?? null
        ]);
    }
}