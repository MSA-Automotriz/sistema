<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RequerimientoCompra extends Model
{
    use HasFactory;
   
    protected $table = 'requerimientos_compra';
   
    protected $fillable = [
        'codigo',
        'tipo',
        'almacen_id',
        'fecha',
        'comentario',
        'estado_id',
        'orden_trabajo',
        'cotizacion_id',
        'prioridad',    
        'user_id',
        'proveedor_id'  // Agregar este campo
    ];
   
    public function almacen()
    {
        return $this->belongsTo(Almacen::class);
    }
   
    public function user()
    {
        return $this->belongsTo(User::class);
    }
   
    public function estado()
    {
        return $this->belongsTo(Estado::class);
    }
   
    // Agregar relación con proveedor
    public function proveedor()
    {
        return $this->belongsTo(Proveedor::class);
    }
   
    public function detalles()
    {
        return $this->hasMany(DetalleRequerimientoCompra::class, 'requerimiento_compra_id');
    }
   
    public function ordenesCompra()
    {
        return $this->hasMany(OrdenCompra::class, 'requerimiento_compra_id');
    }
   
    public function historialActualizaciones()
    {
        return $this->hasMany(HistorialRequerimientoCompra::class, 'requerimiento_compra_id')
            ->orderBy('created_at', 'desc');
    }
   
    public function cotizacion()
    {
        return $this->belongsTo(Cotizacion::class, 'cotizacion_id');
    }
   
    public function getPrioridadFormateadaAttribute()
    {
        switch ($this->prioridad) {
            case 'Alta':
                return '<span class="badge bg-warning">Alta</span>';
            case 'Urgente':
                return '<span class="badge bg-danger">Urgente</span>';
            default:
                return '<span class="badge bg-info">Normal</span>';
        }
    }
}