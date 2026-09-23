<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Almacen extends Model
{
    protected $table = 'almacenes';

    protected $fillable = [
        'nombre',
        'direccion',
        'es_vehiculos',
        'centro_costo_id',
        'parent_id',
        'activo',
    ];

    protected $casts = [
        'es_vehiculos' => 'boolean',
        'activo' => 'boolean',
    ];

    public function centroCosto()
    {
        return $this->belongsTo(CentroCosto::class, 'centro_costo_id');
    }

    public function parent()
    {
        return $this->belongsTo(Almacen::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(Almacen::class, 'parent_id');
    }

    public function allChildren()
    {
        return $this->hasMany(Almacen::class, 'parent_id')->with('allChildren');
    }
}