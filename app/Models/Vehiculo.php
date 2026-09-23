<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Vehiculo extends Model
{
    protected $table = 'catalogos';

    protected $fillable = [
        'marca_id',
        'modelo_id',
        'version_id',
        'anio_modelo_id',
        'fotografia',
        'fecha_compra',
        'precio_compra',
        'nro_factura',
        'serie_vin',
        'color_id',
    ];

    public function marca()
    {
        return $this->belongsTo(Marca::class, 'marca_id');
    }

    public function modelo()
    {
        return $this->belongsTo(Modelo::class, 'modelo_id');
    }

    public function version()
    {
        return $this->belongsTo(Version::class, 'version_id');
    }

    public function anioModelo()
    {
        return $this->belongsTo(AnioModelo::class, 'anio_modelo_id');
    }

    public function color()
    {
        return $this->belongsTo(Color::class, 'color_id');
    }
}