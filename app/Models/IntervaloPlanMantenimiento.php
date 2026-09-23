<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class IntervaloPlanMantenimiento extends Model
{
    use HasFactory;
    
    protected $table = 'intervalo_plan_mantenimientos';
    
    protected $fillable = [
        'plan_mantenimiento_id',
        'componente_plan_id',
        'kilometraje',
        'horas',
        'cantidad_especifica',
        'precio_especifico',
        'moneda_precio',
        'aplica',
        'notas'
    ];

    protected $casts = [
        'cantidad_especifica' => 'decimal:2',
        'precio_especifico' => 'decimal:2',
        'aplica' => 'boolean'
    ];

    // Relaciones
    public function planMantenimiento()
    {
        return $this->belongsTo(PlanMantenimiento::class, 'plan_mantenimiento_id');
    }

    public function componentePlan()
    {
        return $this->belongsTo(ComponentePlanMantenimiento::class, 'componente_plan_id');
    }

    // Scopes
    public function scopeQueAplica($query)
    {
        return $query->where('aplica', true);
    }

    public function scopePorKilometraje($query, $km)
    {
        return $query->where('kilometraje', $km);
    }

    public function scopeEnRango($query, $kmInicio, $kmFin)
    {
        return $query->whereBetween('kilometraje', [$kmInicio, $kmFin]);
    }

    // Accessors
    public function getKilometrajeFormateadoAttribute()
    {
        return number_format($this->kilometraje) . ' km';
    }

    public function getHorasFormateadasAttribute()
    {
        return $this->horas ? number_format($this->horas) . ' hrs' : 'N/A';
    }

    public function getPrecioFormateadoAttribute()
    {
        if (!$this->precio_especifico) return 'N/A';
        
        $simbolo = $this->moneda_precio === 'USD' ? 'US$' : 'S/';
        return $simbolo . ' ' . number_format($this->precio_especifico, 2);
    }

    public function getCantidadFormateadaAttribute()
    {
        if (!$this->cantidad_especifica) {
            return $this->componentePlan->cantidad . ' ' . $this->componentePlan->unidad_medida;
        }
        
        return $this->cantidad_especifica . ' ' . $this->componentePlan->unidad_medida;
    }

    // Métodos de utilidad
    public function calcularHoras()
    {
        if ($this->horas) {
            return $this->horas;
        }
        
        $plan = $this->planMantenimiento;
        if ($plan && $plan->relacion_horas_km) {
            return ($this->kilometraje * $plan->relacion_horas_km) / 5000; // Base 5000km = X horas
        }
        
        return null;
    }

    public function obtenerPrecioFinal()
    {
        return $this->precio_especifico ?? $this->componentePlan->precio_base ?? 0;
    }

    public function obtenerCantidadFinal()
    {
        return $this->cantidad_especifica ?? $this->componentePlan->cantidad ?? 0;
    }
}
