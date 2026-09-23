<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Traslado extends Model
{
    protected $table = 'traslados';
    
    protected $fillable = [
        'almacen_origen_id',
        'almacen_destino_id',
        'motivo',
        'fecha_traslado',
        'estado',
        'usuario_id'
    ];
    
    protected $dates = [
        'fecha_traslado',
        'created_at',
        'updated_at'
    ];
    
    public function almacenOrigen()
    {
        return $this->belongsTo(Almacen::class, 'almacen_origen_id');
    }
    
    public function almacenDestino()
    {
        return $this->belongsTo(Almacen::class, 'almacen_destino_id');
    }
    
    public function usuario()
    {
        return $this->belongsTo(User::class, 'usuario_id');
    }
    
    public function items()
    {
        return $this->hasMany(TrasladoItem::class);
    }
}