<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class EstadoRequerimiento extends Model
{
    protected $table = 'estados'; // Cambiar a 'estados' en lugar de 'estado_requerimientos'
    protected $fillable = ['nombre', 'descripcion'];
    
    public function requerimientos()
    {
        return $this->hasMany(RequerimientoCompra::class, 'estado_id');
    }
}