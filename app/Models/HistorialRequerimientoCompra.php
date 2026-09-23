<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HistorialRequerimientoCompra extends Model
{
    use HasFactory;

    protected $fillable = [
        'requerimiento_compra_id',
        'user_id',
        'estado_id',
        'estado_nombre',
        'estado_color',
        'comentario'
    ];

    public function requerimiento()
    {
        return $this->belongsTo(RequerimientoCompra::class, 'requerimiento_compra_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function estado()
    {
        return $this->belongsTo(Estado::class);
    }
}