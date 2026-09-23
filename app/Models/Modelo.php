<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Modelo extends Model
{
    protected $table = 'modelos';

    protected $fillable = [
        'marca_id',
        'nombre',
        'duracion_garantia',
        'cantidad_anos',
        'ficha_tecnica',
    ];

    public function marca()
    {
        return $this->belongsTo(Marca::class);
    }

    public function versiones()
    {
        return $this->hasMany(Version::class);
    }
}