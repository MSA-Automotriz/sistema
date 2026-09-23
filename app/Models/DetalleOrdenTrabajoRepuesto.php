<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DetalleOrdenTrabajoRepuesto extends Model
{
    use HasFactory;

    protected $table = 'detalle_orden_trabajo_repuestos';
    
    protected $fillable = [
        'orden_trabajo_id',
        'parte_id',
        'cantidad',
        'precio_unitario',
        'subtotal',
        'descripcion',
        'notas',
    ];

    /**
     * Relación con la orden de trabajo
     */
    public function ordenTrabajo()
    {
        return $this->belongsTo(OrdenTrabajoMantenimiento::class, 'orden_trabajo_id');
    }

    /**
     * Relación con la parte o repuesto
     */
    public function parte()
    {
        return $this->belongsTo(Parte::class, 'parte_id');
    }

    /**
     * Boot method para calcular subtotal automáticamente
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($detalle) {
            if (!isset($detalle->subtotal)) {
                $detalle->subtotal = $detalle->cantidad * $detalle->precio_unitario;
            }
        });

        static::updating(function ($detalle) {
            if ($detalle->isDirty(['cantidad', 'precio_unitario']) && !$detalle->isDirty('subtotal')) {
                $detalle->subtotal = $detalle->cantidad * $detalle->precio_unitario;
            }
        });
    }

    /**
     * Accessor para obtener el subtotal calculado
     */
    public function getSubtotalCalculadoAttribute()
    {
        return $this->cantidad * $this->precio_unitario;
    }
}