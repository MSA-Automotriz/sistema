<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pago extends Model
{
    use HasFactory;

    protected $table = 'pagos';

    protected $fillable = [
        'cotizacion_id',
        'concepto',
        'monto',
        'moneda',
        'fecha_pago',
        'tipo',
        'medio_pago',
        'comprobante',
        'observaciones',
        'user_id'
    ];

    protected $casts = [
        'fecha_pago' => 'date',
        'monto' => 'decimal:2'
    ];

    /**
     * Obtiene la cotización asociada al pago
     */
    public function cotizacion()
    {
        return $this->belongsTo(Cotizacion::class);
    }

    /**
     * Obtiene el usuario que registró el pago
     */
    public function usuario()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}