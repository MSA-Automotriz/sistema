<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EstadoCotizacion extends Model
{
    use HasFactory;

    protected $table = 'estados_cotizacion';

    protected $fillable = [
        'nombre',
        'descripcion',
        'color',
    ];

    /**
     * Obtiene todas las cotizaciones con este estado
     */
    public function cotizaciones()
    {
        return $this->hasMany(Cotizacion::class, 'estado_id');
    }
}