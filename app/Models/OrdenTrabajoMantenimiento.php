<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrdenTrabajoMantenimiento extends Model
{
    use HasFactory;

    protected $table = 'ordenes_trabajo_mantenimiento';
    
    protected $fillable = [
        'codigo_orden', 
        'vehiculo_id',
        'cliente_id',
        'cita_id',
        'fecha_ingreso',
        'fecha_diagnostico',
        'fecha_aprobacion_cliente',
        'fecha_inicio_trabajo',
        'fecha_fin_trabajo',
        'fecha_entrega',
        'descripcion_problema',
        'diagnostico',
        'recomendaciones',
        'estado',
        'tecnico_asignado_id',
        'aprobado_por_cliente',
        'metodo_aprobacion',
        'kilometraje_ingreso',
        'kilometraje_salida',
        'box',
    ];

    protected $casts = [
        'fecha_ingreso' => 'datetime',
        'fecha_diagnostico' => 'datetime',
        'fecha_aprobacion_cliente' => 'datetime',
        'fecha_inicio_trabajo' => 'datetime',
        'fecha_fin_trabajo' => 'datetime',
        'fecha_entrega' => 'datetime',
        'aprobado_por_cliente' => 'boolean',
    ];

    // Relaciones
    public function vehiculo()
    {
        return $this->belongsTo(VehiculoMantenimiento::class, 'vehiculo_id');
    }

    public function cliente()
    {
        return $this->belongsTo(Cliente::class, 'cliente_id');
    }

    public function cita()
    {
        return $this->belongsTo(CitaMantenimiento::class, 'cita_id');
    }

    public function tecnico()
    {
        return $this->belongsTo(User::class, 'tecnico_asignado_id');
    }

    public function detallesRepuestos()
    {
        return $this->hasMany(DetalleOrdenTrabajoRepuesto::class, 'orden_trabajo_id');
    }

    public function detallesServicios()
    {
        return $this->hasMany(DetalleOrdenTrabajoServicio::class, 'orden_trabajo_id');
    }

    public function factura()
    {
        return $this->hasOne(FacturaOrdenTrabajo::class, 'orden_trabajo_id');
    }

    // Atributos calculados
    public function getTotalRepuestosAttribute()
    {
        return $this->detallesRepuestos->sum(function ($detalle) {
            return $detalle->cantidad * $detalle->precio_unitario;
        });
    }

    public function getTotalServiciosAttribute()
    {
        return $this->detallesServicios->sum(function ($detalle) {
            return $detalle->cantidad * $detalle->precio_unitario;
        });
    }

    public function getTotalOrdenAttribute()
    {
        return $this->getTotalRepuestosAttribute() + $this->getTotalServiciosAttribute();
    }

    public function seguimientos()
    {
        return $this->hasMany(SeguimientoOrdenTrabajo::class, 'orden_trabajo_id');
    }
}