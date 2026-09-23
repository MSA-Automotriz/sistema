<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Version extends Model
{
    protected $table = 'versiones';

    protected $fillable = [
        'marca_id',
        'modelo_id',
        'nombre',
        'carroceria',
        'cilindrada',
        'transmision',
        'traccion',
        'combustible_id',
    ];

    public function marca()
    {
        return $this->belongsTo(Marca::class);
    }

    public function modelo()
    {
        return $this->belongsTo(Modelo::class);
    }

    public function combustible()
    {
        return $this->belongsTo(Combustible::class);
    }

    public function aniosModelo()
    {
        return $this->hasMany(AnioModelo::class);
    }
}