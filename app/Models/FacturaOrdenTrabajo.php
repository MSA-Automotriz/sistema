<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class FacturaOrdenTrabajo extends Model
{
    protected $table = 'facturas_orden_trabajo';
    
    protected $fillable = [
        'orden_trabajo_id',
        'numero_factura',
        'fecha_emision',
        'subtotal',
        'impuestos',
        'total',
        'metodo_pago',
        'estado_pago', // pendiente, pagado, anulado
        'notas',
        'dias_garantia',
    ];
    
    public function ordenTrabajo()
    {
        return $this->belongsTo(OrdenTrabajoMantenimiento::class, 'orden_trabajo_id');
    }
}