<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class TipoMovimiento extends Model
{
    protected $table = 'tipos_movimiento';
    
    protected $fillable = [
        'codigo',
        'nombre',
        'descripcion',
        'afecta_stock'
    ];
    
    public function movimientos()
    {
        return $this->hasMany(Movimiento::class);
    }
}