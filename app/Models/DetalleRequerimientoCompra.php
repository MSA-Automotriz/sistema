<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class DetalleRequerimientoCompra extends Model
{
    protected $table = 'detalles_requerimientos_compra';
    
    protected $fillable = [
        'requerimiento_compra_id',
        'item_id',
        'tipo_item',
        'cantidad',
        'descripcion',   // Campo adicional para descripciones personalizadas
        'color_id',      // Referencia al color para vehículos
        'cotizacion_detalle_id', // Referencia al detalle de cotización original
    ];
    
    public function requerimiento()
    {
        return $this->belongsTo(RequerimientoCompra::class, 'requerimiento_compra_id');
    }
    
    public function item()
    {
        return $this->morphTo('item', 'tipo_item', 'item_id');
    }
    
    /**
     * Relación con el detalle de cotización original
     */
    public function detalleCotizacion()
    {
        return $this->belongsTo(DetalleCotizacion::class, 'cotizacion_detalle_id');
    }
    
    /**
     * Relación con el color (para vehículos)
     */
    public function color()
    {
        return $this->belongsTo(Color::class, 'color_id');
    }
    
    /**
     * Obtiene el nombre del ítem
     */
    public function getNombreAttribute()
    {
        // Si hay una descripción personalizada, usarla
        if (!empty($this->descripcion)) {
            return $this->descripcion;
        }
        
        if ($this->tipo_item === 'parte') {
            return $this->item->nombre ?? 'Repuesto sin nombre';
        } elseif ($this->tipo_item === 'vehiculo') {
            $vehiculo = $this->item;
            if (!$vehiculo) {
                return 'Vehículo desconocido';
            }
            
            $nombreVehiculo = implode(' ', array_filter([
                $vehiculo->marca?->nombre ?? '',
                $vehiculo->modelo?->nombre ?? '',
                $vehiculo->version?->nombre ?? '',
                $vehiculo->anioModelo?->anio ?? '',
            ]));
            
            // Añadir color si está disponible
            if ($this->color) {
                $nombreVehiculo .= " ({$this->color->nombre})";
            }
            
            return $nombreVehiculo ?: 'Vehículo sin datos';
        }
        
        return 'Ítem desconocido';
    }
    
    /**
     * Obtiene el código del ítem
     */
    public function getCodigoAttribute()
    {
        if ($this->tipo_item === 'parte') {
            return $this->item->codigo ?? 'N/A';
        } elseif ($this->tipo_item === 'vehiculo') {
            return "V{$this->item->id}";
        }
        
        return 'N/A';
    }
    
    /**
     * Obtiene el tipo formateado del ítem
     */
    public function getTipoFormateadoAttribute()
    {
        if ($this->tipo_item === 'parte') {
            return 'Repuesto';
        } elseif ($this->tipo_item === 'vehiculo') {
            return 'Vehículo';
        }
        
        return 'Otro';
    }
}