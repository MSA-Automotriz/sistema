<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DetalleCotizacion extends Model
{
    use HasFactory;

    protected $table = 'detalles_cotizacion';
   
    protected $fillable = [
        'cotizacion_id',
        'vehiculo_catalogo_id',
        'color_id',
        'repuesto_id',
        'servicio_id',
        'cantidad',
        'precio_unitario',
        'descuento',
        'subtotal',
        'total',
    ];

    /**
     * Relaciones
     */
    public function cotizacion()
    {
        return $this->belongsTo(Cotizacion::class);
    }

    /**
     * Obtiene el vehículo del catálogo
     */
    public function vehiculo()
    {
        return $this->belongsTo(Vehiculo::class, 'vehiculo_catalogo_id');
    }

    /**
     * Alias para vehiculo
     */
    public function vehiculoCatalogo()
    {
        return $this->vehiculo();
    }

    /**
     * Obtiene el repuesto asociado
     */
    public function repuesto()
    {
        return $this->belongsTo(Parte::class, 'repuesto_id');
    }

    /**
     * Obtiene el servicio asociado
     */
    public function servicio()
    {
        return $this->belongsTo(Servicio::class, 'servicio_id');
    }

    /**
     * Obtiene el color seleccionado
     */
    public function color()
    {
        return $this->belongsTo(Color::class);
    }

    /**
     * Métodos de cálculo
     */
    public function calcularSubtotal()
    {
        return $this->precio_unitario * $this->cantidad;
    }

    public function calcularDescuento()
    {
        return $this->subtotal * ($this->descuento / 100);
    }

    public function calcularTotal()
    {
        return $this->subtotal - $this->calcularDescuento();
    }

    /**
     * Atributos calculados para información del vehículo
     */
    public function getMarcaAttribute()
    {
        return optional($this->vehiculo)->marca;
    }

    public function getModeloAttribute()
    {
        return optional($this->vehiculo)->modelo;
    }

    public function getVersionAttribute()
    {
        return optional($this->vehiculo)->version;
    }

    public function getAnioModeloAttribute()
    {
        return optional($this->vehiculo)->anioModelo;
    }

    /**
     * Descripción completa del vehículo
     */
    public function getDescripcionVehiculoAttribute()
    {
        $vehiculo = $this->vehiculo;
        if (!$vehiculo) return 'Sin información de vehículo';

        return sprintf(
            "%s %s %s %s", 
            optional($vehiculo->marca)->nombre ?? 'Sin marca',
            optional($vehiculo->modelo)->nombre ?? 'Sin modelo',
            optional($vehiculo->version)->nombre ?? 'Sin versión',
            optional($vehiculo->anioModelo)->nombre ?? 'Sin año'
        );
    }
}