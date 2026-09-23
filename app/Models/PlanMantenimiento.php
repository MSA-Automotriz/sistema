<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PlanMantenimiento extends Model
{
    use HasFactory;
    
    protected $table = 'plan_mantenimientos';
    
    protected $fillable = [
        'nombre',
        'descripcion',
        'modelo_vehiculo',
        'ano_modelo',
        'tipo_transmision',
        'tono_vehiculo',
        'intervalo_base',
        'kilometraje_maximo',
        'relacion_horas_km',
        'tarifa_mano_obra',
        'impuestos',
        'margen_beneficio',
        'moneda_principal',
        'proveedor_predeterminado_id',
        'mostrar_precios',
        'activo',
        'user_id'
    ];

    protected $casts = [
        'tarifa_mano_obra' => 'decimal:2',
        'impuestos' => 'decimal:2',
        'margen_beneficio' => 'decimal:2',
        'mostrar_precios' => 'boolean',
        'activo' => 'boolean'
    ];

    // Relaciones
    public function proveedorPredeterminado()
    {
        return $this->belongsTo(Proveedor::class, 'proveedor_predeterminado_id');
    }

    public function usuario()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function componentesPlan()
    {
        return $this->hasMany(ComponentePlanMantenimiento::class, 'plan_mantenimiento_id');
    }

    public function intervalos()
    {
        return $this->hasMany(IntervaloPlanMantenimiento::class, 'plan_mantenimiento_id');
    }

    // Scopes
    public function scopeActivo($query)
    {
        return $query->where('activo', true);
    }

    public function scopePorModelo($query, $modelo)
    {
        return $query->where('modelo_vehiculo', 'like', "%{$modelo}%");
    }

    public function scopePorAno($query, $ano)
    {
        return $query->where('ano_modelo', $ano);
    }

    // Accessors
    public function getNombreCompletoAttribute()
    {
        return "{$this->nombre} - {$this->modelo_vehiculo} {$this->ano_modelo} ({$this->tipo_transmision})";
    }

    public function getIntervaloTextoAttribute()
    {
        return "Cada {$this->intervalo_base} km hasta {$this->kilometraje_maximo} km";
    }

    // Métodos de utilidad
    public function generarIntervalos()
    {
        $intervalos = [];
        for ($km = $this->intervalo_base; $km <= $this->kilometraje_maximo; $km += $this->intervalo_base) {
            $intervalos[] = $km;
        }
        return $intervalos;
    }

    public function calcularCostoTotal($intervalos = null)
    {
        if (!$intervalos) {
            $intervalos = $this->generarIntervalos();
        }

        $costoTotal = 0;
        foreach ($this->componentesPlan as $componente) {
            $costoTotal += $componente->calcularCostoEnIntervalos($intervalos);
        }

        return $costoTotal;
    }
}
