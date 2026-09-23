<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;

class TipoCambio extends Model
{
    protected $table = 'tipos_cambio';

    protected $fillable = [
        'fecha',
        'compra',
        'venta',
        'fecha_inicio',
        'fecha_fin',
        'origen',
        'activo',
        'observaciones',
        'user_id',
    ];

    protected $casts = [
        'fecha' => 'date',
        'fecha_inicio' => 'date', 
        'fecha_fin' => 'date',
        'compra' => 'decimal:4',
        'venta' => 'decimal:4',
        'activo' => 'boolean',
    ];

    // Relaciones
    public function usuario(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // Scopes
    public function scopeActivo($query)
    {
        return $query->where('activo', true);
    }

    public function scopeVigente($query, $fecha = null)
    {
        $fecha = $fecha ? Carbon::parse($fecha) : Carbon::now();
        
        return $query->where('fecha_inicio', '<=', $fecha)
                    ->where(function ($q) use ($fecha) {
                        $q->whereNull('fecha_fin')
                          ->orWhere('fecha_fin', '>=', $fecha);
                    });
    }

    public function scopeDeSunat($query)
    {
        return $query->where('origen', 'sunat');
    }

    public function scopeManual($query)
    {
        return $query->where('origen', 'manual');
    }

    // Métodos estáticos
    public static function obtenerActual($fecha = null)
    {
        return static::activo()
                     ->vigente($fecha)
                     ->orderBy('fecha', 'desc')
                     ->first();
    }

    public static function obtenerPorFecha($fecha)
    {
        return static::activo()
                     ->where('fecha', $fecha)
                     ->first();
    }

    public static function convertir($monto, $monedaOrigen, $monedaDestino, $tipoOperacion = 'venta')
    {
        if ($monedaOrigen === $monedaDestino) {
            return $monto;
        }

        $tipoCambio = static::obtenerActual();
        
        if (!$tipoCambio) {
            throw new \Exception('No hay tipo de cambio disponible');
        }

        $tasa = $tipoOperacion === 'compra' ? $tipoCambio->compra : $tipoCambio->venta;

        if ($monedaOrigen === 'USD' && $monedaDestino === 'PEN') {
            return $monto * $tasa;
        } elseif ($monedaOrigen === 'PEN' && $monedaDestino === 'USD') {
            return $monto / $tasa;
        }

        throw new \Exception('Conversión de moneda no soportada');
    }

    // Accessors
    public function getEsVigenteAttribute()
    {
        $hoy = Carbon::now();
        return $this->fecha_inicio <= $hoy && 
               ($this->fecha_fin === null || $this->fecha_fin >= $hoy);
    }

    public function getOrigenTextoAttribute()
    {
        return $this->origen === 'sunat' ? 'SUNAT' : 'Manual';
    }

    public function getEstadoTextoAttribute()
    {
        if (!$this->activo) {
            return 'Inactivo';
        }
        
        return $this->es_vigente ? 'Vigente' : 'No vigente';
    }
}
