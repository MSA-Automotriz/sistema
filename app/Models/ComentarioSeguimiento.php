<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ComentarioSeguimiento extends Model
{
    use HasFactory;

    protected $table = 'comentarios_seguimiento';

    protected $fillable = [
        'seguimiento_id',
        'user_id',
        'contenido',
        'archivo'
    ];

    public function seguimiento()
    {
        return $this->belongsTo(SeguimientoCotizacion::class, 'seguimiento_id');
    }

    public function usuario()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}