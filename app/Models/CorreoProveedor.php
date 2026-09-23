<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CorreoProveedor extends Model
{
    protected $table = 'correos_proveedores';

    protected $fillable = ['proveedor_id', 'correo'];

    public function proveedor()
    {
        return $this->belongsTo(Proveedor::class);
    }
}