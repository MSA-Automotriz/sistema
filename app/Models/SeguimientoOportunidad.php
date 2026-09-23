<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SeguimientoOportunidad extends Model
{
    use HasFactory;

    protected $table = 'seguimientos_oportunidades';

    protected $fillable = [
        'oportunidad_id',
        'user_id',
        'tipo',
        'contenido',
        'fecha_seguimiento',
        'recordatorio',
        'fecha_recordatorio',
    ];

    protected $casts = [
        'fecha_seguimiento' => 'datetime',
        'fecha_recordatorio' => 'datetime',
        'recordatorio' => 'boolean',
    ];

    public function oportunidad()
    {
        return $this->belongsTo(Oportunidad::class);
    }

    public function usuario()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}