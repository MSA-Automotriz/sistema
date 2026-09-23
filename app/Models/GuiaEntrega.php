<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class GuiaEntrega extends Model
{
    use HasFactory;
    
    protected $table = 'guias_entrega';
    
    protected $fillable = [
        'numero',
        'fecha',
        'proveedor_id',
        'transportista',
        'placa_vehiculo',
        'conductor',
        'dni_conductor',
        'observaciones',
        'estado',
        'usuario_id',
        'recibido_por',
        'fecha_recepcion',
        'total'
    ];
    
    protected $casts = [
        'fecha' => 'date',
        'fecha_recepcion' => 'datetime',
        'total' => 'decimal:2'
    ];
    
    public function proveedor()
    {
        return $this->belongsTo(Proveedor::class);
    }
    
    public function usuario()
    {
        return $this->belongsTo(User::class);
    }
    
    public function recibidoPor()
    {
        return $this->belongsTo(User::class, 'recibido_por');
    }
    
    public function detalles()
    {
        return $this->hasMany(DetalleGuiaEntrega::class);
    }
    
    protected static function boot()
    {
        parent::boot();
        
        static::saved(function ($guia) {
            $guia->calcularTotal();
        });
    }
    
    public function calcularTotal()
    {
        $total = $this->detalles()->sum('subtotal');
        $this->update(['total' => $total]);
    }
    
    public static function generarNumero()
    {
        $anio = date('Y');
        $mes = date('m');
        
        $ultimo = self::whereYear('created_at', $anio)
            ->whereMonth('created_at', $mes)
            ->orderBy('id', 'desc')
            ->first();
        
        $numero = $ultimo ? intval(substr($ultimo->numero, -6)) + 1 : 1;
        
        return 'GE-' . $anio . $mes . str_pad($numero, 6, '0', STR_PAD_LEFT);
    }
    
    public function scopePendientes($query)
    {
        return $query->where('estado', 'pendiente');
    }
    
    public function scopeRecibidas($query)
    {
        return $query->where('estado', 'recibida');
    }
    
    public function scopePorProveedor($query, $proveedorId)
    {
        return $query->where('proveedor_id', $proveedorId);
    }
    
    public function getEstadoColorAttribute()
    {
        return match($this->estado) {
            'pendiente' => 'warning',
            'en_transito' => 'info',
            'recibida' => 'success',
            'cancelada' => 'danger',
            default => 'secondary'
        };
    }
}