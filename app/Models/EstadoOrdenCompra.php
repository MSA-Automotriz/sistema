<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EstadoOrdenCompra extends Model
{
    protected $table = 'estado_ordenes_compra';

    protected $fillable = ['nombre', 'descripcion'];

    public function ordenes()
    {
        return $this->hasMany(OrdenCompra::class, 'estado_id');
    }
}