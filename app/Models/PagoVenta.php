<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class PagoVenta extends Model
{
    use HasFactory;
    
    protected $table = 'pagos_ventas';
    
    protected $fillable = [
        'venta_id',
        'numero_pago',
        'fecha_pago',
        'monto',
        'moneda',
        'tipo_cambio',
        'monto_convertido',
        'metodo_pago',
        'referencia_pago',
        'banco',
        'observaciones',
        'usuario_id',
        'validado',
        'validado_por',
        'fecha_validacion'
    ];
    
    protected $casts = [
        'fecha_pago' => 'date',
        'monto' => 'decimal:2',
        'tipo_cambio' => 'decimal:4',
        'monto_convertido' => 'decimal:2',
        'validado' => 'boolean',
        'fecha_validacion' => 'datetime'
    ];
    
    // Relaciones
    public function venta()
    {
        return $this->belongsTo(Venta::class);
    }
    
    public function usuario()
    {
        return $this->belongsTo(User::class, 'usuario_id');
    }
    
    public function validadoPor()
    {
        return $this->belongsTo(User::class, 'validado_por');
    }
    
    // Métodos auxiliares
    public static function generarNumeroPago()
    {
        $anio = date('Y');
        $mes = date('m');
        $ultimoPago = self::whereYear('created_at', $anio)
            ->whereMonth('created_at', $mes)
            ->orderBy('id', 'desc')
            ->first();
        
        $numero = $ultimoPago ? intval(substr($ultimoPago->numero_pago, -6)) + 1 : 1;
        return 'PV-' . $anio . $mes . str_pad($numero, 6, '0', STR_PAD_LEFT);
    }
    
    /**
     * Calcula el monto convertido según el tipo de cambio
     */
    public function calcularMontoConvertido($monedaVenta)
    {
        if ($this->moneda === $monedaVenta) {
            $this->monto_convertido = $this->monto;
        } elseif ($this->tipo_cambio) {
            if ($this->moneda === 'USD' && $monedaVenta === 'PEN') {
                $this->monto_convertido = $this->monto * $this->tipo_cambio;
            } elseif ($this->moneda === 'PEN' && $monedaVenta === 'USD') {
                $this->monto_convertido = $this->monto / $this->tipo_cambio;
            }
        }
        
        return $this->monto_convertido;
    }
    
    /**
     * Marcar como validado
     */
    public function validar($comentario = null)
    {
        $this->validado = true;
        $this->validado_por = Auth::id();
        $this->fecha_validacion = now();
        if ($comentario) {
            $this->observaciones = ($this->observaciones ? $this->observaciones . "\n" : '') . 
                                 "Validado: " . $comentario;
        }
        return $this->save();
    }
    
    // Scopes
    public function scopeValidados($query)
    {
        return $query->where('validado', true);
    }
    
    public function scopePendientesValidacion($query)
    {
        return $query->where('validado', false);
    }
    
    public function scopePorMetodo($query, $metodo)
    {
        return $query->where('metodo_pago', $metodo);
    }
}