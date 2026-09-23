<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrdenCompra extends Model
{
    use HasFactory;
    
    protected $table = 'orden_compras';
    
    protected $fillable = [
        'codigo',
        'requerimiento_compra_id',
        'tipo',
        'estado',
        'almacen_destino_id',
        'requerido_por',
        'aprobado_por',
        'proveedor_id',
        'moneda',
        'observaciones',
        'fecha_aprobacion',
        'total',
        'estado_recepcion'  // AGREGAR
    ];

    public function requerimiento()
    {
        return $this->belongsTo(RequerimientoCompra::class, 'requerimiento_compra_id');
    }

    public function almacen()
    {
        return $this->belongsTo(Almacen::class, 'almacen_destino_id');
    }

    public function usuario()
    {
        return $this->belongsTo(User::class, 'requerido_por');
    }

    public function aprobador()
    {
        return $this->belongsTo(User::class, 'aprobado_por');
    }

    public function proveedor()
    {
        return $this->belongsTo(Proveedor::class, 'proveedor_id');
    }

    public function detalles()
    {
        return $this->hasMany(DetalleOrdenCompra::class);
    }

    public function actualizarEstadoRecepcion()
    {
        $totalDetalles = $this->detalles->count();
        $detallesCompletos = $this->detalles->where('estado_recepcion', 'completo')->count();
        $detallesParciales = $this->detalles->where('estado_recepcion', 'parcial')->count();
        
        if ($detallesCompletos == $totalDetalles) {
            $this->estado_recepcion = 'completo';
        } elseif ($detallesCompletos > 0 || $detallesParciales > 0) {
            $this->estado_recepcion = 'parcial';
        } else {
            $this->estado_recepcion = 'pendiente';
        }
        $this->save();
    }
}