<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Oportunidad extends Model
{
    use HasFactory;

    protected $table = 'oportunidades';

    protected $fillable = [
        'titulo',
        'cliente_id',
        'probabilidad',
        'valor_estimado',
        'moneda',
        'descripcion',
        'estado',
        'user_id',
        'fecha_cierre_estimada',
    ];

    protected $casts = [
        'fecha_cierre_estimada' => 'date',
    ];

    public function cliente()
    {
        return $this->belongsTo(Cliente::class);
    }

    public function usuario()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function seguimientos()
    {
        return $this->hasMany(SeguimientoOportunidad::class);
    }
}