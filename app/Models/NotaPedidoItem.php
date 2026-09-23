<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NotaPedidoItem extends Model
{
    use HasFactory;

    protected $table = 'nota_pedido_items';

    protected $fillable = [
        'nota_pedido_id',
        'item_id',
        'item_type',
        'tipo',
        'descripcion',
        'cantidad',
        'precio_unitario',
        'detalles',
        'subtipo'
    ];

    protected $casts = [
        'cantidad' => 'decimal:2',
        'precio_unitario' => 'decimal:2'
    ];

    /**
     * Obtiene la nota de pedido a la que pertenece el item
     */
    public function notaPedido()
    {
        return $this->belongsTo(NotaPedido::class);
    }

    /**
     * Relación polimórfica para obtener el item relacionado (vehículo, servicio, parte)
     */
    public function item()
    {
        return $this->morphTo();
    }

    /**
     * Calcula el subtotal del item
     */
    public function getSubtotalAttribute()
    {
        return $this->cantidad * $this->precio_unitario;
    }
}