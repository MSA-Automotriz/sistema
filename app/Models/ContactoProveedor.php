<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ContactoProveedor extends Model
{
    protected $table = 'contactos_proveedores';

    protected $fillable = ['proveedor_id', 'nombre', 'telefono'];

    public function proveedor()
    {
        return $this->belongsTo(Proveedor::class);
    }
}