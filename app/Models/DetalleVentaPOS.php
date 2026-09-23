<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DetalleVentaPOS extends Model
{
    use HasFactory;

    protected $table = 'detalles_venta';

    protected $fillable = [
        'venta_id',
        'parte_id',
        'cantidad',
        'precio_unitario',
        'descuento',
        'subtotal',
        'total',
        'tipo_item',
        'descripcion'
    ];

    protected $casts = [
        'cantidad' => 'float',
        'precio_unitario' => 'float',
        'descuento' => 'float',
        'subtotal' => 'float',
        'total' => 'float',
    ];

    // Relaciones
    public function venta()
    {
        return $this->belongsTo(Venta::class);
    }

    public function parte()
    {
        return $this->belongsTo(Parte::class);
    }

    // Mutators y Accessors
    public function getDescuentoPorcentajeAttribute()
    {
        return $this->descuento ?? 0;
    }

    public function getDescuentoMontoAttribute()
    {
        return ($this->precio_unitario * $this->cantidad) - $this->subtotal;
    }

    // Scopes
    public function scopePartes($query)
    {
        return $query->where('tipo_item', 'parte');
    }

    public function scopeServicios($query)
    {
        return $query->where('tipo_item', 'servicio');
    }
}