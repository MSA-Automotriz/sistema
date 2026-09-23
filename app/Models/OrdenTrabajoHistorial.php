<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrdenTrabajoHistorial extends Model
{
    // Especifica el nombre correcto de la tabla
    protected $table = 'orden_trabajo_historial';

    protected $fillable = [
        'orden_trabajo_id',
        'usuario_id',
        'accion',
        'descripcion',
        'detalles', // Añade este campo según la estructura de tabla que mostraste
        'fecha'
    ];

    protected $casts = [
        'fecha' => 'datetime',
        'detalles' => 'array' // Si quieres manejar el campo detalles como un array JSON
    ];

    public function ordenTrabajo()
    {
        return $this->belongsTo(OrdenTrabajo::class);
    }

    public function usuario()
    {
        return $this->belongsTo(User::class);
    }
}