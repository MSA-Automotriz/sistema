<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class DetalleGuiaEntrega extends Model
{
    use HasFactory;
    
    protected $table = 'detalle_guias_entrega';
    
    protected $fillable = [
        'guia_entrega_id',
        'producto_id',
        'tipo_producto',
        'codigo_producto',
        'nombre_producto',
        'cantidad_enviada',
        'cantidad_recibida',
        'precio_unitario',
        'subtotal',
        'observaciones_detalle'
    ];
    
    protected $casts = [
        'cantidad_enviada' => 'decimal:2',
        'cantidad_recibida' => 'decimal:2',
        'precio_unitario' => 'decimal:2',
        'subtotal' => 'decimal:2'
    ];
    
    public function guiaEntrega()
    {
        return $this->belongsTo(GuiaEntrega::class);
    }
    
    public function producto()
    {
        return $this->morphTo();
    }
    
    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($detalle) {
            if (!$detalle->subtotal) {
                $detalle->subtotal = $detalle->cantidad_enviada * $detalle->precio_unitario;
            }
            if (!$detalle->cantidad_recibida) {
                $detalle->cantidad_recibida = $detalle->cantidad_enviada;
            }
        });
        
        static::updating(function ($detalle) {
            if ($detalle->isDirty(['cantidad_enviada', 'precio_unitario'])) {
                $detalle->subtotal = $detalle->cantidad_enviada * $detalle->precio_unitario;
            }
        });
        
        static::saved(function ($detalle) {
            $detalle->guiaEntrega->calcularTotal();
        });
        
        static::deleted(function ($detalle) {
            $detalle->guiaEntrega->calcularTotal();
        });
    }
    
    public function getDiferenciaAttribute()
    {
        return $this->cantidad_enviada - $this->cantidad_recibida;
    }
    
    public function getEsCompleta()
    {
        return $this->cantidad_enviada == $this->cantidad_recibida;
    }
    
    public function getEstadoRecepcionAttribute()
    {
        if ($this->cantidad_recibida == 0) {
            return 'no_recibido';
        } elseif ($this->cantidad_recibida < $this->cantidad_enviada) {
            return 'parcial';
        } elseif ($this->cantidad_recibida == $this->cantidad_enviada) {
            return 'completo';
        } else {
            return 'exceso';
        }
    }
}