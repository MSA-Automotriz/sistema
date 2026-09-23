<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NotaPedido extends Model
{
    use HasFactory;

    protected $table = 'notas_pedido';

    protected $fillable = [
        'cotizacion_id',
        'codigo',
        'fecha_emision',
        'estado',
        'observaciones',
        'user_id'
    ];

    protected $casts = [
        'fecha_emision' => 'date'
    ];

    /**
     * Obtiene la cotización asociada a la nota de pedido
     */
    public function cotizacion()
    {
        return $this->belongsTo(Cotizacion::class);
    }

    /**
     * Obtiene los items de la nota de pedido
     */
    public function items()
    {
        return $this->hasMany(NotaPedidoItem::class);
    }

    /**
     * Obtiene el usuario que creó la nota de pedido
     */
    public function usuario()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Genera el código de la nota de pedido
     */
    public static function generarCodigo()
    {
        $anio = date('Y');
        $mes = date('m');
        $ultimaNota = self::whereYear('created_at', $anio)
            ->whereMonth('created_at', $mes)
            ->orderBy('id', 'desc')
            ->first();
        $numero = $ultimaNota ? intval(substr($ultimaNota->codigo, -6)) + 1 : 1;
        return 'NP-' . $anio . $mes . str_pad($numero, 6, '0', STR_PAD_LEFT);
    }
}