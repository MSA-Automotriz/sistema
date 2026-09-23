<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DetalleDevolucionProveedor extends Model
{
    use HasFactory;
    
    protected $table = 'detalles_devolucion_proveedor';
    
    protected $fillable = [
        'devolucion_proveedor_id',
        'item_id',
        'tipo_item',
        'cantidad',
        'motivo_detalle'
    ];
    
    public function devolucion()
    {
        return $this->belongsTo(DevolucionProveedor::class, 'devolucion_proveedor_id');
    }
    
    public function item()
    {
        return $this->morphTo('item', 'tipo_item', 'item_id');
    }
    
    public function getNombreAttribute()
    {
        if ($this->tipo_item === 'parte') {
            return $this->item->nombre;
        } elseif ($this->tipo_item === 'vehiculo') {
            return implode(' ', array_filter([
                $this->item->marca?->nombre ?? '',
                $this->item->modelo?->nombre ?? '',
                $this->item->version?->nombre ?? '',
                $this->item->anioModelo?->anio ?? '',
            ]));
        }
        return 'Ítem desconocido';
    }
    
    public function getCodigoAttribute()
    {
        if ($this->tipo_item === 'parte') {
            return $this->item->codigo;
        } elseif ($this->tipo_item === 'vehiculo') {
            return "V{$this->item->id}";
        }
        return 'N/A';
    }
}