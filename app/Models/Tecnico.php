<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tecnico extends Model
{
    use HasFactory;

    protected $table = 'tecnicos';

    protected $fillable = [
        'user_id',
        'codigo',
        'especialidad',
        'cedula_profesional',
        'telefono',
        'telefono_emergencia',
        'certificaciones',
        'habilidades',
        'fecha_ingreso',
        'estado',
        'notas',
    ];

    protected $casts = [
        'fecha_ingreso' => 'date',
    ];

    // Relaciones
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function ordenesTrabajoMantenimiento()
    {
        return $this->hasMany(OrdenTrabajoMantenimiento::class, 'tecnico_asignado_id', 'user_id');
    }

    // Accesor para obtener el nombre completo
    public function getNombreCompletoAttribute()
    {
        return $this->user->name;
    }

    // Accesor para obtener el email
    public function getEmailAttribute()
    {
        return $this->user->email;
    }

    // Scope para técnicos activos
    public function scopeActivos($query)
    {
        return $query->where('estado', 'activo');
    }
}
