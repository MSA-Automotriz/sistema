<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SeguimientoCotizacion extends Model
{
    use HasFactory;

    protected $table = 'seguimientos_cotizacion';

    protected $fillable = [
        'cotizacion_id',
        'user_id',
        'tipo',
        'contenido',
        'fecha_seguimiento',
        'recordatorio',
        'fecha_recordatorio',
        'realizado',
        'datos_adicionales',
    ];

    protected $casts = [
        'fecha_seguimiento' => 'datetime',
        'fecha_recordatorio' => 'datetime',
        'realizado' => 'boolean',
        'datos_adicionales' => 'array',
    ];

    public function cotizacion()
    {
        return $this->belongsTo(Cotizacion::class);
    }

    public function usuario()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function comentarios()
    {
        return $this->hasMany(ComentarioSeguimiento::class, 'seguimiento_id');
    }
}