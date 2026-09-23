<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TrasladoItem extends Model
{
    protected $table = 'traslado_items';
    
    protected $fillable = [
        'traslado_id',
        'parte_id',
        'vehiculo_id',
        'tipo_item',
        'cantidad'
    ];
    
    public function traslado()
    {
        return $this->belongsTo(Traslado::class);
    }
    
    public function parte()
    {
        return $this->belongsTo(Parte::class);
    }
    
    public function vehiculo()
    {
        return $this->belongsTo(Vehiculo::class);
    }
    
    // Método para obtener el nombre del elemento
    public function getNombreItemAttribute()
    {
        if ($this->tipo_item === 'parte') {
            return $this->parte->nombre ?? 'N/A';
        } else {
            $vehiculo = $this->vehiculo;
            return $vehiculo ? 
                ($vehiculo->marca->nombre ?? '') . ' ' . 
                ($vehiculo->modelo->nombre ?? '') . ' ' . 
                ($vehiculo->version->nombre ?? '') . ' ' . 
                ($vehiculo->anioModelo->anio ?? '') : 'N/A';
        }
    }
    
    // Método para obtener el código del elemento
    public function getCodigoItemAttribute()
    {
        if ($this->tipo_item === 'parte') {
            return $this->parte->codigo ?? 'N/A';
        } else {
            return $this->vehiculo->id ?? 'N/A';
        }
    }
}