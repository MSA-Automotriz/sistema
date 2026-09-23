<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HistorialCotizacion extends Model
{
    use HasFactory;

    protected $table = 'historial_cotizaciones';

    protected $fillable = [
        'cotizacion_id',
        'estado_anterior_id',
        'estado_nuevo_id',
        'user_id',
        'comentario',
    ];

    /**
     * Obtiene la cotización a la que pertenece este registro histórico
     */
    public function cotizacion()
    {
        return $this->belongsTo(Cotizacion::class);
    }

    /**
     * Obtiene el estado anterior de la cotización
     */
    public function estadoAnterior()
    {
        return $this->belongsTo(EstadoCotizacion::class, 'estado_anterior_id');
    }

    /**
     * Obtiene el nuevo estado de la cotización
     */
    public function estadoNuevo()
    {
        return $this->belongsTo(EstadoCotizacion::class, 'estado_nuevo_id');
    }

    /**
     * Obtiene el usuario que realizó el cambio de estado
     */
    public function usuario()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}