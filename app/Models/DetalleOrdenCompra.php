<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DetalleOrdenCompra extends Model
{
    use HasFactory;
    
    protected $table = 'detalle_orden_compras';
    
    protected $fillable = [
        'orden_compra_id',
        'item_id',
        'tipo_item',
        'codigo',
        'nombre_producto',
        'cantidad_requerida',
        'cantidad_en_compra',
        'unidad',
        'precio_compra',
        'descuento',
        'total',
        'afecto_igv',
        'cantidad_recibida',  // AGREGAR
        'estado_recepcion'    // AGREGAR
    ];

    public function ordenCompra()
    {
        return $this->belongsTo(OrdenCompra::class);
    }

    // NUEVA RELACIÓN: para obtener el item (parte o vehículo) de forma polimórfica
    public function item()
    {
        if ($this->tipo_item === 'parte') {
            return $this->belongsTo(Parte::class, 'item_id');
        } else if ($this->tipo_item === 'vehiculo') {
            return $this->belongsTo(Vehiculo::class, 'item_id');
        }
        return null;
    }

    public function getItem()
    {
        if ($this->tipo_item === 'parte') {
            return Parte::find($this->item_id);
        } else if ($this->tipo_item === 'vehiculo') {
            return Vehiculo::find($this->item_id);
        }
        return null;
    }

    public function recepciones()
    {
        return $this->hasMany(RecepcionOrdenCompra::class);
    }

    public function getCantidadPendienteAttribute()
    {
        return $this->cantidad_en_compra - ($this->cantidad_recibida ?? 0);
    }

    public function actualizarEstadoRecepcion()
    {
        $cantidadRecibida = $this->cantidad_recibida ?? 0;
        
        if ($cantidadRecibida == 0) {
            $this->estado_recepcion = 'pendiente';
        } elseif ($cantidadRecibida < $this->cantidad_en_compra) {
            $this->estado_recepcion = 'parcial';
        } else {
            $this->estado_recepcion = 'completo';
        }
        $this->save();
    }
    
    /**
     * Estados posibles para estado_recepcion:
     * - pendiente: No se ha recibido nada
     * - parcial: Se ha recibido una cantidad menor a la solicitada
     * - completo: Se ha recibido la cantidad exacta solicitada
     * - completo_con_faltantes: Se marcó como completo pero faltan items
     */
    public function getEstadosRecepcionAttribute()
    {
        return [
            'pendiente' => 'Pendiente',
            'parcial' => 'Parcial', 
            'completo' => 'Completo',
            'completo_con_faltantes' => 'Completo con faltantes'
        ];
    }

    public function devoluciones()
    {
        return $this->hasMany(DevolucionOrdenCompra::class);
    }
    public function inventario()
    {
        $query = Inventario::where('almacen_id', $this->ordenCompra->almacen_destino_id);
        
        if ($this->tipo_item === 'vehiculo') {
            return $query->where('vehiculo_id', $this->item_id)->whereNull('parte_id');
        } else {
            return $query->where('parte_id', $this->item_id)->whereNull('vehiculo_id');
        }
    }
}