<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Estado extends Model
{
    use HasFactory;

    protected $fillable = [
        'nombre',
        'color',
        'tipo'
    ];

    public function requerimientos()
    {
        return $this->hasMany(RequerimientoCompra::class);
    }

    public function ordenesCompra()
    {
        return $this->hasMany(OrdenCompra::class);
    }
}