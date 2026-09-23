<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

class SeguimientoOrdenTrabajo extends Model
{
    use HasFactory;

    protected $table = 'seguimientos_orden_trabajo';

    protected $fillable = [
        'orden_trabajo_id',
        'user_id',
        'tipo',
        'contenido',
        'fecha_seguimiento',
        'recordatorio',
        'fecha_recordatorio',
        'realizado'
    ];

    protected $casts = [
        'fecha_seguimiento' => 'datetime',
        'fecha_recordatorio' => 'datetime',
        'recordatorio' => 'boolean',
        'realizado' => 'boolean',
    ];

    // Relaciones
    public function ordenTrabajo()
    {
        return $this->belongsTo(OrdenTrabajoMantenimiento::class, 'orden_trabajo_id');
    }

    public function usuario()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function comentarios()
    {
        return $this->hasMany(ComentarioSeguimientoOrden::class, 'seguimiento_id');
    }

    // Mutadores
    public function setFechaSeguimientoAttribute($value)
    {
        $this->attributes['fecha_seguimiento'] = $value ?: now();
    }

    // Accesor
    public function getEsVencidoAttribute()
    {
        if ($this->recordatorio && $this->fecha_recordatorio) {
            return $this->fecha_recordatorio->isPast() && !$this->realizado;
        }
        return false;
    }

    // Scopes
    public function scopeRecientes($query)
    {
        return $query->orderBy('fecha_seguimiento', 'desc');
    }

    public function scopePendientes($query)
    {
        return $query->where('realizado', false);
    }

    public function scopeConRecordatorio($query)
    {
        return $query->where('recordatorio', true);
    }

    public function scopeVencidos($query)
    {
        return $query->where('recordatorio', true)
                     ->where('realizado', false)
                     ->where('fecha_recordatorio', '<', now());
    }

    public function scopePorVencer($query, $dias = 3)
    {
        $fechaLimite = Carbon::now()->addDays($dias);
        
        return $query->where('recordatorio', true)
                     ->where('realizado', false)
                     ->where('fecha_recordatorio', '>=', now())
                     ->where('fecha_recordatorio', '<=', $fechaLimite);
    }

    public function scopePorTipo($query, $tipo)
    {
        return $query->where('tipo', $tipo);
    }

    // Métodos útiles
    public function toggleRealizado()
    {
        $this->realizado = !$this->realizado;
        $this->save();
        return $this;
    }
}