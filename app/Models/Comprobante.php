<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Comprobante extends Model
{
    use HasFactory;

    protected $table = 'comprobantes';

    protected $fillable = [
        'cotizacion_id',
        'tipo',
        'serie',
        'numero',
        'fecha_emision',
        'monto',
        'moneda',
        'detalle',
        'archivo',
        'user_id'
    ];

    protected $casts = [
        'fecha_emision' => 'date',
        'monto' => 'decimal:2'
    ];

    /**
     * Obtiene la cotización asociada al comprobante
     */
    public function cotizacion()
    {
        return $this->belongsTo(Cotizacion::class);
    }

    /**
     * Obtiene el usuario que registró el comprobante
     */
    public function usuario()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}