<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AnioModelo extends Model
{
    protected $table = 'anios_modelo';

    protected $fillable = [
        'marca_id',
        'modelo_id',
        'version_id',
        'anio',
        'precio',
        'moneda',
    ];

    public function marca()
    {
        return $this->belongsTo(Marca::class);
    }

    public function modelo()
    {
        return $this->belongsTo(Modelo::class);
    }

    public function version()
    {
        return $this->belongsTo(Version::class);
    }
}