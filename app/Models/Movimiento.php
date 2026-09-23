<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Movimiento extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'tipo_movimiento_id',
        'parte_id',
        'almacen_id',
        'centro_costo_id',
        'cantidad',
        'stock_anterior',
        'stock_resultante',
        'documento_tipo',     // Orden de compra, Requerimiento, Traslado, etc.
        'documento_id',       // ID del documento relacionado
        'documento_referencia',
        'fecha_movimiento',
        'usuario_id',
        'observaciones',
        'costo_unitario',     // Importante para valorización de inventario
    ];
    
    protected $dates = ['fecha_movimiento'];
    
    public function tipoMovimiento()
    {
        return $this->belongsTo(TipoMovimiento::class);
    }
    
    public function parte()
    {
        return $this->belongsTo(Parte::class);
    }

    public function vehiculo()
    {
        return $this->belongsTo(Vehiculo::class);
    }
    
    public function almacen()
    {
        return $this->belongsTo(Almacen::class);
    }
    
    public function centroCosto()
    {
        return $this->belongsTo(CentroCosto::class);
    }
    
    public function usuario()
    {
        return $this->belongsTo(User::class, 'usuario_id');
    }
    
    // Polimorfico para relacionar con cualquier documento
    public function documento()
    {
        return $this->morphTo('documento', 'documento_tipo', 'documento_id');
    }
    
    // Accessor para obtener el número del documento
    public function getNumeroDocumentoAttribute()
    {
        if ($this->documento_referencia) {
            return $this->documento_referencia;
        }
        
        if ($this->documento) {
            switch ($this->documento_tipo) {
                case 'devolucion_proveedor':
                    return $this->documento->codigo ?? 'N/A';
                default:
                    return $this->documento->numero ?? $this->documento->codigo ?? 'N/A';
            }
        }
        
        return 'N/A';
    }
}