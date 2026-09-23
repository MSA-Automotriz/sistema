<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DevolucionProveedor extends Model
{
    use HasFactory;
    
    protected $table = 'devoluciones_proveedor';
    
    protected $fillable = [
        'codigo',
        'proveedor_id',
        'motivo',
        'fecha_emision',
        'observaciones',
        'estado',
        'usuario_id',
        'almacen_id'
    ];
    
    protected $casts = [
        'fecha_emision' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];
    
    public function proveedor()
    {
        return $this->belongsTo(Proveedor::class);
    }
    
    public function usuario()
    {
        return $this->belongsTo(User::class, 'usuario_id');
    }
    
    public function almacen()
    {
        return $this->belongsTo(Almacen::class);
    }
    
    public function detalles()
    {
        return $this->hasMany(DetalleDevolucionProveedor::class, 'devolucion_proveedor_id');
    }
    
    // Método para generar un código único para esta devolución
    public static function generarCodigo()
    {
        $anio = date('Y');
        $mes = date('m');
        $ultimaDevolucion = self::whereYear('created_at', $anio)
            ->whereMonth('created_at', $mes)
            ->orderBy('id', 'desc')
            ->first();
        
        $numero = $ultimaDevolucion ? intval(substr($ultimaDevolucion->codigo, -6)) + 1 : 1;
        return 'DEV-' . $anio . $mes . str_pad($numero, 6, '0', STR_PAD_LEFT);
    }
}