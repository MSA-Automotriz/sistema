<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DevolucionOrdenCompra extends Model
{
    protected $table = 'devoluciones_orden_compra';
    
    protected $fillable = [
        'detalle_orden_compra_id',
        'cantidad_devuelta',
        'motivo',
        'fecha_devolucion',
        'devuelto_por'
    ];
    
    protected $casts = [
        'fecha_devolucion' => 'date'
    ];
    
    public function detalleOrdenCompra()
    {
        return $this->belongsTo(DetalleOrdenCompra::class);
    }
    
    public function devueltoPor()
    {
        return $this->belongsTo(User::class, 'devuelto_por');
    }
}