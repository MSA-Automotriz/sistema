<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DetalleTraslado extends Model
{
    protected $table = 'detalle_traslados';

    protected $fillable = [
        'traslado_id',
        'parte_id',
        'cantidad',
        'inventario_origen_id',
        'inventario_destino_id'
    ];

    public function traslado()
    {
        return $this->belongsTo(Traslado::class);
    }

    public function parte()
    {
        return $this->belongsTo(Parte::class);
    }

    public function inventarioOrigen()
    {
        return $this->belongsTo(Inventario::class, 'inventario_origen_id');
    }

    public function inventarioDestino()
    {
        return $this->belongsTo(Inventario::class, 'inventario_destino_id');
    }
}