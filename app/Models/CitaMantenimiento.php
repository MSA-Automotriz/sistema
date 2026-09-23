<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class CitaMantenimiento extends Model
{
    use HasFactory;
    
    protected $table = 'citas_mantenimiento';
    
    // Aseguramos que los timestamps estén habilitados correctamente
    public $timestamps = true;
    
    protected $fillable = [
        'vehiculo_id',
        'cliente_id',
        'fecha_hora_cita',
        'motivo_visita',
        'descripcion_problema',
        'estado',
        'tecnico_id',
        'notas_adicionales',
    ];
    
    protected $casts = [
        'fecha_hora_cita' => 'datetime',
    ];
    
    // Relaciones
    public function vehiculo()
    {
        return $this->belongsTo(VehiculoMantenimiento::class, 'vehiculo_id');
    }
    
    public function cliente()
    {
        return $this->belongsTo(Cliente::class, 'cliente_id');
    }
    
    public function tecnico()
    {
        return $this->belongsTo(User::class, 'tecnico_id');
    }
    
    public function ordenTrabajo()
    {
        return $this->hasOne(OrdenTrabajoMantenimiento::class, 'cita_id');
    }
    
    // Métodos adicionales
    public function getPendiente()
    {
        return $this->estado === 'pendiente';
    }
    
    public function getConfirmada()
    {
        return $this->estado === 'confirmada';
    }
    
    public function getCancelada()
    {
        return $this->estado === 'cancelada';
    }
    
    public function getCompletada()
    {
        return $this->estado === 'completada';
    }
    
    public function tieneOrdenTrabajo()
    {
        return $this->ordenTrabajo()->exists();
    }
}