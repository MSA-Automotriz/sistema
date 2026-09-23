<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DocumentoPlaca extends Model
{
    use HasFactory;

    protected $table = 'documentos_placa';

    protected $fillable = [
        'placa_id', // Ahora relacionado con la placa específica
        'cotizacion_id', // Mantenemos también relación directa con cotización
        'nombre',
        'tipo', // Tipos actualizados
        'fecha',
        'archivo',
        'observaciones',
        'user_id'
    ];

    protected $casts = [
        'fecha' => 'date'
    ];

    // Tipos de documentos
    const TIPOS = [
        'rotativa' => 'Placa rotativa',
        'definitiva' => 'Placa definitiva',
        'guia_remision' => 'Guía de remisión',
        'otros' => 'Otros'
    ];

    /**
     * Obtiene la placa asociada al documento
     */
    public function placa()
    {
        return $this->belongsTo(PlacaInfo::class, 'placa_id');
    }

    /**
     * Obtiene la cotización asociada al documento
     */
    public function cotizacion()
    {
        return $this->belongsTo(Cotizacion::class);
    }

    /**
     * Obtiene el usuario que creó el documento
     */
    public function usuario()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Obtiene el texto del tipo de documento
     */
    public function getTipoTextoAttribute()
    {
        return self::TIPOS[$this->tipo] ?? 'Desconocido';
    }
}