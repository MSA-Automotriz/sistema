<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PlacaInfo extends Model
{
    use HasFactory;

    protected $table = 'placas';

    protected $fillable = [
        'cotizacion_id',
        'numero_placa',
        'tipo_placa',
        'fecha_emision',
        'estado_placa',
        'paso_actual',
        'observaciones',
        'user_id'
    ];

    protected $casts = [
        'fecha_emision' => 'date'
    ];

    const ESTADOS = [
        1 => 'Pendiente de pago',
        2 => 'En producción',
        3 => 'En camino, pendiente de pago',
        4 => 'Por recoger',
        5 => 'Entregado'
    ];

    const TIPOS = [
        'rotativa' => 'Rotativa',
        'definitiva' => 'Definitiva'
    ];

    public function cotizacion()
    {
        return $this->belongsTo(Cotizacion::class);
    }

    public function usuario()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function documentos()
    {
        return $this->hasMany(DocumentoPlaca::class, 'placa_id');
    }

    public function comentarios()
    {
        return $this->hasMany(PlacaComentario::class, 'placa_id');
    }

    public function getEstadoTextoAttribute()
    {
        return self::ESTADOS[$this->paso_actual] ?? 'Desconocido';
    }

    public function getTipoTextoAttribute()
    {
        return self::TIPOS[$this->tipo_placa] ?? 'Desconocido';
    }
}