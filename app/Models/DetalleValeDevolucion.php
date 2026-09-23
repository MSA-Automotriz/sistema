<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DetalleValeDevolucion extends Model
{
    use HasFactory;
    
    protected $table = 'detalle_vales_devolucion';
    
    protected $fillable = [
        'vale_devolucion_id',
        'producto_id',
        'tipo_producto',
        'codigo_producto',
        'nombre_producto',
        'cantidad',
        'precio_unitario',
        'subtotal',
        'motivo_detalle',
        'observaciones_detalle'
    ];
    
    protected $casts = [
        'cantidad' => 'decimal:2',
        'precio_unitario' => 'decimal:2',
        'subtotal' => 'decimal:2'
    ];
    
    // Relaciones
    public function valeDevolucion()
    {
        return $this->belongsTo(ValeDevolucion::class);
    }
    
    public function producto()
    {
        if ($this->tipo_producto === 'parte') {
            return $this->belongsTo(Parte::class, 'producto_id');
        } elseif ($this->tipo_producto === 'vehiculo') {
            return $this->belongsTo(Vehiculo::class, 'producto_id');
        }
        return null;
    }
    
    // Métodos
    public function calcularSubtotal()
    {
        $this->subtotal = $this->cantidad * $this->precio_unitario;
        $this->save();
        return $this->subtotal;
    }
    
    // Boot para calcular subtotal automáticamente
    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($detalle) {
            $detalle->subtotal = $detalle->cantidad * $detalle->precio_unitario;
        });
        
        static::updating(function ($detalle) {
            if ($detalle->isDirty(['cantidad', 'precio_unitario'])) {
                $detalle->subtotal = $detalle->cantidad * $detalle->precio_unitario;
            }
        });
        
        static::saved(function ($detalle) {
            // Actualizar el total del vale cuando se guarda un detalle
            $detalle->valeDevolucion()->first()->calcularTotal();
        });
        
        static::deleted(function ($detalle) {
            // Actualizar el total del vale cuando se elimina un detalle
            if ($detalle->valeDevolucion()->first()) {
                $detalle->valeDevolucion()->first()->calcularTotal();
            }
        });
    }
}