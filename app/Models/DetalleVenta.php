<?php
// app/Models/DetalleVenta.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DetalleVenta extends Model
{
    use HasFactory;

    protected $table = 'detalles_venta'; 
    
    protected $fillable = [
        'venta_id',
        'parte_id',
        'servicio_id',
        'vehiculo_id',
        'descripcion',
        'cantidad',
        'precio_unitario',
        'descuento',
        'subtotal',
        'total',
        'tipo_item'
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
    
    public function servicio()
    {
        return $this->belongsTo(Servicio::class);
    }
    
    public function vehiculo()
    {
        return $this->belongsTo(Vehiculo::class);
    }
    
    // Atributo para el nombre del item según su tipo
    public function getNombreItemAttribute()
    {
        if ($this->parte_id) {
            return $this->parte ? $this->parte->nombre : 'Parte no disponible';
        } elseif ($this->servicio_id) {
            return $this->servicio ? $this->servicio->nombre : 'Servicio no disponible';
        } elseif ($this->vehiculo_id) {
            return $this->vehiculo ? $this->vehiculo->nombre_completo : 'Vehículo no disponible';
        } else {
            return $this->descripcion;
        }
    }
    
    // Atributo para el código del item según su tipo
    public function getCodigoItemAttribute()
    {
        if ($this->parte_id) {
            return $this->parte ? $this->parte->codigo : 'N/A';
        } elseif ($this->servicio_id) {
            return 'SERV-' . str_pad($this->servicio_id, 4, '0', STR_PAD_LEFT);
        } elseif ($this->vehiculo_id) {
            return $this->vehiculo ? $this->vehiculo->codigo : 'N/A';
        } else {
            return 'N/A';
        }
    }
}