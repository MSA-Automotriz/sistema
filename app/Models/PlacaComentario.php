<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PlacaComentario extends Model
{
    use HasFactory;

    protected $table = 'placa_comentarios';

    protected $fillable = [
        'placa_id',
        'cotizacion_id',
        'comentario',
        'user_id',
    ];

    public function placa()
    {
        return $this->belongsTo(PlacaInfo::class, 'placa_id');
    }

    public function cotizacion()
    {
        return $this->belongsTo(Cotizacion::class);
    }

    public function usuario()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}