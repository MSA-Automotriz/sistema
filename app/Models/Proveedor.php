<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Proveedor extends Model
{
    use HasFactory;
    
    protected $table = 'proveedores';
    
    protected $fillable = [
        'tipo_documento',
        'numero_documento',
        'apellido_paterno',
        'apellido_materno',
        'nombres',
        'razon_social',
        'direccion',
        'departamento',
        'provincia',
        'distrito',
        'categoria_proveedor_id',
        'cubre_garantias',
        'es_aseguradora',
    ];
    
    public function categoriaProveedor()
    {
        return $this->belongsTo(CategoriaProveedor::class);
    }
    
    public function correos()
    {
        return $this->hasMany(CorreoProveedor::class);
    }
    
    public function contactos()
    {
        return $this->hasMany(ContactoProveedor::class);
    }
    
    public function cuentas()
    {
        return $this->hasMany(CuentaProveedor::class);
    }
    
    public function ordenesCompra()
    {
        return $this->hasMany(OrdenCompra::class);
    }
    
    // Método para mostrar el nombre completo o razón social
    public function getNombreCompletoAttribute()
    {
        if (!empty($this->razon_social)) {
            return $this->razon_social;
        }
        
        return trim($this->nombres . ' ' . $this->apellido_paterno . ' ' . $this->apellido_materno);
    }
    
    // Método para obtener el documento formateado
    public function getDocumentoFormateadoAttribute()
    {
        return $this->tipo_documento . ': ' . $this->numero_documento;
    }
}