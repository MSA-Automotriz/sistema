<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DetalleOrdenTrabajoServicio extends Model
{
    use HasFactory;

    protected $table = 'detalle_orden_trabajo_servicios';
    
    protected $fillable = [
        'orden_trabajo_id',
        'servicio_id',
        'cantidad',
        'precio_unitario',
        'subtotal',
        'descripcion',
        'tiempo_estimado',
        'tiempo_real',
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
     * Relación con el servicio
     */
    public function servicio()
    {
        return $this->belongsTo(Servicio::class, 'servicio_id');
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