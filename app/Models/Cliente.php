<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Cliente extends Model
{
    protected $fillable = [
        'documento_identidad', 'tipo_cliente', 'apellido_paterno', 'apellido_materno', 'nombres',
        'razon_social', 'departamento', 'provincia', 'distrito', 'correo', 
        'categoria_cliente_id', 'canal_captacion_id'
    ];

    public function categoria()
    {
        return $this->belongsTo(CategoriaCliente::class, 'categoria_cliente_id');
    }

    public function canalCaptacion()
    {
        return $this->belongsTo(CanalCaptacion::class, 'canal_captacion_id');
    }

    public function telefonos()
    {
        return $this->hasMany(Telefono::class);
    }
}