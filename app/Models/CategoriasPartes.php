<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CategoriasPartes extends Model
{
    protected $table = 'categorias_partes'; // Especificamos el nombre de la tabla

    protected $fillable = [
        'nombre',
        'descripcion',
        'descuento',
    ];
}