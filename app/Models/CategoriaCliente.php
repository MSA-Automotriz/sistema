<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CategoriaCliente extends Model
{
    protected $fillable = ['nombre'];

    public function clientes()
    {
        return $this->hasMany(Cliente::class, 'categoria_cliente_id');
    }
}