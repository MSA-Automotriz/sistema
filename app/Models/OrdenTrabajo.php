<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\User;

class OrdenTrabajo extends Model
{
    protected $fillable = [
        'cotizacion_id',
        'tecnico_id',
        'fecha_inicio',
        'fecha_fin_estimada',
        'fecha_fin_real',
        'descripcion',
        'estado',
        'observaciones',
        'vehiculo_id',
        'box'
    ];
protected $casts = [
    'fecha_inicio' => 'datetime',
    'fecha_fin_estimada' => 'datetime',
    'fecha_fin_real' => 'datetime',
];

public function cotizacion(): BelongsTo
{
    return $this->belongsTo(Cotizacion::class, 'cotizacion_id');
}
public function vehiculo()
{
    return $this->belongsTo(VehiculoMantenimiento::class, 'vehiculo_id');
}
public function historial(): HasMany
{
    return $this->hasMany(OrdenTrabajoHistorial::class);
}
}