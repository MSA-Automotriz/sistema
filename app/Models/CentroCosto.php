<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CentroCosto extends Model
{
    protected $table = 'centros_costos';

    protected $fillable = [
        'codigo',
        'nombre',
        'descripcion',
    ];
}