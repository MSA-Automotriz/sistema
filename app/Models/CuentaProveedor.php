<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CuentaProveedor extends Model
{
    protected $table = 'cuentas_proveedores';

    protected $fillable = [
        'proveedor_id',
        'banco_id',
        'moneda',
        'tipo_cuenta',
        'numero_cuenta',
        'cci',
    ];

    public function proveedor()
    {
        return $this->belongsTo(Proveedor::class);
    }

    public function banco()
    {
        return $this->belongsTo(Banco::class);
    }
}