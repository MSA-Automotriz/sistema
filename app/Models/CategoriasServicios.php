<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CategoriasServicios extends Model
{
    use HasFactory;

    protected $table = 'categorias_servicios_tercerizados';
    
    protected $fillable = [
        'nombre'
    ];

    // Relación con servicios tercerizados
    public function servicios()
    {
        return $this->hasMany(Servicio::class, 'categoria_id');
    }
}