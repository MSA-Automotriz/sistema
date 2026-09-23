<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Telefono extends Model
{
    protected $fillable = ['cliente_id', 'numero', 'tipo'];

    public function cliente()
    {
        return $this->belongsTo(Cliente::class);
    }
}