<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class ReglaVencimientoCotizacion extends Model
{
    use HasFactory;
    
    protected $table = 'reglas_vencimiento_cotizaciones';
    
    protected $fillable = [
        'nombre',
        'descripcion',
        'dias_vencimiento',
        'dias_alerta',
        'estado_vencido_id',
        'permite_reasignacion',
        'requiere_aprobacion',
        'notificar_vencimiento',
        'activo',
        'condiciones'
    ];
    
    protected $casts = [
        'permite_reasignacion' => 'boolean',
        'requiere_aprobacion' => 'boolean',
        'notificar_vencimiento' => 'boolean',
        'activo' => 'boolean',
        'condiciones' => 'array'
    ];
    
    /**
     * Relación con el estado de cotización vencida
     */
    public function estadoVencido()
    {
        return $this->belongsTo(EstadoCotizacion::class, 'estado_vencido_id');
    }
    
    /**
     * Relación con cotizaciones que usan esta regla
     */
    public function cotizaciones()
    {
        return $this->hasMany(Cotizacion::class, 'regla_vencimiento_id');
    }
    
    /**
     * Calcula la fecha de vencimiento basada en una fecha de referencia
     */
    public function calcularFechaVencimiento(?Carbon $fechaReferencia = null): Carbon
    {
        $fecha = $fechaReferencia ?? now();
        return $fecha->copy()->addDays($this->dias_vencimiento);
    }
    
    /**
     * Calcula la fecha de alerta basada en una fecha de referencia
     */
    public function calcularFechaAlerta(?Carbon $fechaReferencia = null): ?Carbon
    {
        if ($this->dias_alerta <= 0) {
            return null;
        }
        
        $fecha = $fechaReferencia ?? now();
        return $fecha->copy()->addDays($this->dias_vencimiento - $this->dias_alerta);
    }
    
    /**
     * Verifica si esta regla aplica para un usuario específico
     */
    public function aplicaParaUsuario($userId): bool
    {
        if (!$this->activo || !$this->condiciones) {
            return $this->activo;
        }
        
        $condiciones = $this->condiciones;
        
        // Si no hay condiciones específicas, aplica para todos
        if (empty($condiciones['usuarios']) && empty($condiciones['roles'])) {
            return true;
        }
        
        // Verificar usuarios específicos
        if (!empty($condiciones['usuarios']) && in_array($userId, $condiciones['usuarios'])) {
            return true;
        }
        
        // Verificar roles (se implementaría según el sistema de roles)
        if (!empty($condiciones['roles'])) {
            $user = \App\Models\User::find($userId);
            if ($user && method_exists($user, 'hasAnyRole') && $user->hasAnyRole($condiciones['roles'])) {
                return true;
            }
        }
        
        return false;
    }
    
    /**
     * Scope para reglas activas
     */
    public function scopeActivas($query)
    {
        return $query->where('activo', true);
    }
    
    /**
     * Scope para reglas que permiten reasignación
     */
    public function scopeConReasignacion($query)
    {
        return $query->where('permite_reasignacion', true);
    }
}
