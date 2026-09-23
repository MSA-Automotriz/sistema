<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Servicio extends Model
{
    use HasFactory;

    protected $table = 'servicios';

    protected $fillable = [
        'nombre',
        'precio',
        'moneda',
        'categoria_id'
    ];

    public function categoria()
    {
        return $this->belongsTo(CategoriasServicios::class, 'categoria_id');
    }
}
