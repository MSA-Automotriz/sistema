<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ValeDevolucion extends Model
{
    use HasFactory;
    
    protected $table = 'vales_devolucion';
    
    protected $fillable = [
        'numero',
        'fecha',
        'proveedor_id',
        'motivo',
        'observaciones',
        'estado',
        'usuario_id',
        'aprobado_por',
        'fecha_aprobacion',
        'total'
    ];
    
    protected $casts = [
        'fecha' => 'date',
        'fecha_aprobacion' => 'datetime',
        'total' => 'decimal:2'
    ];
    
    // Relaciones
    public function proveedor()
    {
        return $this->belongsTo(Proveedor::class);
    }
    
    public function usuario()
    {
        return $this->belongsTo(User::class, 'usuario_id');
    }
    
    public function aprobadoPor()
    {
        return $this->belongsTo(User::class, 'aprobado_por');
    }
    
    public function detalles()
    {
        return $this->hasMany(DetalleValeDevolucion::class);
    }
    
    // Métodos
    public static function generarNumero()
    {
        $anio = date('Y');
        $mes = date('m');
        
        $ultimo = self::whereYear('created_at', $anio)
            ->whereMonth('created_at', $mes)
            ->orderBy('id', 'desc')
            ->first();
        
        $numero = $ultimo ? intval(substr($ultimo->numero, -6)) + 1 : 1;
        
        return 'VD-' . $anio . $mes . str_pad($numero, 6, '0', STR_PAD_LEFT);
    }
    
    public function calcularTotal()
    {
        $this->total = $this->detalles()->sum('subtotal');
        $this->save();
        return $this->total;
    }
    
    public function aprobar($userId)
    {
        $this->estado = 'aprobado';
        $this->aprobado_por = $userId;
        $this->fecha_aprobacion = now();
        $this->save();
    }
    
    public function rechazar()
    {
        $this->estado = 'rechazado';
        $this->save();
    }
    
    public function procesar()
    {
        $this->estado = 'procesado';
        $this->save();
    }
    
    // Scopes
    public function scopePendientes($query)
    {
        return $query->where('estado', 'pendiente');
    }
    
    public function scopeAprobados($query)
    {
        return $query->where('estado', 'aprobado');
    }
    
    public function scopePorProveedor($query, $proveedorId)
    {
        return $query->where('proveedor_id', $proveedorId);
    }
}