<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ComponentePlanMantenimiento extends Model
{
    use HasFactory;
    
    protected $table = 'componente_plan_mantenimientos';
    
    protected $fillable = [
        'plan_mantenimiento_id',
        'parte_id',
        'cantidad',
        'unidad_medida',
        'accion',
        'proveedor_id',
        'precio_base',
        'moneda',
        'observaciones',
        'activo'
    ];

    protected $casts = [
        'cantidad' => 'decimal:2',
        'precio_base' => 'decimal:2',
        'activo' => 'boolean'
    ];

    // Relaciones
    public function planMantenimiento()
    {
        return $this->belongsTo(PlanMantenimiento::class, 'plan_mantenimiento_id');
    }

    public function parte()
    {
        return $this->belongsTo(Parte::class, 'parte_id');
    }

    public function proveedor()
    {
        return $this->belongsTo(Proveedor::class, 'proveedor_id');
    }

    public function intervalos()
    {
        return $this->hasMany(IntervaloPlanMantenimiento::class, 'componente_plan_id');
    }

    // Scopes
    public function scopeActivo($query)
    {
        return $query->where('activo', true);
    }

    public function scopePorAccion($query, $accion)
    {
        return $query->where('accion', $accion);
    }

    // Accessors
    public function getAccionTextoAttribute()
    {
        $acciones = [
            'Reemplazar' => 'R',
            'Inspeccionar' => 'I',
            'Lubricar' => 'L'
        ];
        
        return $acciones[$this->accion] ?? $this->accion;
    }

    public function getPrecioFormateadoAttribute()
    {
        if (!$this->precio_base) return 'N/A';
        
        $simbolo = $this->moneda === 'USD' ? 'US$' : 'S/';
        return $simbolo . ' ' . number_format($this->precio_base, 2);
    }

    // Métodos de utilidad
    public function calcularPrecioEnIntervalo($kilometraje)
    {
        $intervalo = $this->intervalos()->where('kilometraje', $kilometraje)->first();
        
        if ($intervalo && $intervalo->aplica) {
            return $intervalo->precio_especifico ?? $this->precio_base ?? 0;
        }
        
        return 0;
    }

    public function calcularCostoEnIntervalos($intervalos)
    {
        $costoTotal = 0;
        
        foreach ($intervalos as $km) {
            $costoTotal += $this->calcularPrecioEnIntervalo($km);
        }
        
        return $costoTotal;
    }

    public function aplicaEnIntervalo($kilometraje)
    {
        $intervalo = $this->intervalos()->where('kilometraje', $kilometraje)->first();
        return $intervalo && $intervalo->aplica;
    }
}
