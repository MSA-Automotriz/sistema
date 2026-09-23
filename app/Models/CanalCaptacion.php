<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CanalCaptacion extends Model
{
    protected $table = 'canal_captacion';
    protected $fillable = ['nombre'];

    public function clientes()
    {
        return $this->hasMany(Cliente::class, 'canal_captacion_id');
    }
}