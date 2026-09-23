<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ClasificacionVehiculo extends Model
{
    protected $table = 'clasificaciones_vehiculos';

    protected $fillable = ['nombre'];
}