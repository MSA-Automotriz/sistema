<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ActaEntrega extends Model
{
    use HasFactory;

    protected $table = 'actas_entrega';

    protected $fillable = [
        'cotizacion_id',
        'codigo',
        'fecha_entrega',
        'persona_entrega',
        'vehiculo_detalle',
        'placa',
        'kilometraje',
        'nivel_combustible',
        'estado',
        'observaciones',
        'documento_firmado',
        'user_id',
        // Campos de checklist
        'check_manual',
        'check_garantia',
        'check_tarjeta',
        'check_soat',
        'check_llave',
        'check_gata',
        'check_rueda',
        'check_herramientas',
        'check_carroceria',
        'check_pintura',
        'check_lunas',
        'check_llantas',
        'check_asientos',
        'check_tablero',
        'check_radio',
        'check_climatizacion',
        'check_motor',
        'check_luces',
        'check_frenos',
        'check_direccion',
        'check_bateria',
        'check_arranque'
    ];

    protected $casts = [
        'fecha_entrega' => 'date',
        'kilometraje' => 'integer',
        'nivel_combustible' => 'integer',
        'check_manual' => 'boolean',
        'check_garantia' => 'boolean',
        'check_tarjeta' => 'boolean',
        'check_soat' => 'boolean',
        'check_llave' => 'boolean',
        'check_gata' => 'boolean',
        'check_rueda' => 'boolean',
        'check_herramientas' => 'boolean',
        'check_carroceria' => 'boolean',
        'check_pintura' => 'boolean',
        'check_lunas' => 'boolean',
        'check_llantas' => 'boolean',
        'check_asientos' => 'boolean',
        'check_tablero' => 'boolean',
        'check_radio' => 'boolean',
        'check_climatizacion' => 'boolean',
        'check_motor' => 'boolean',
        'check_luces' => 'boolean',
        'check_frenos' => 'boolean',
        'check_direccion' => 'boolean',
        'check_bateria' => 'boolean',
        'check_arranque' => 'boolean'
    ];

    /**
     * Obtiene la cotización asociada al acta de entrega
     */
    public function cotizacion()
    {
        return $this->belongsTo(Cotizacion::class);
    }

    /**
     * Obtiene el usuario que creó el acta de entrega
     */
    public function usuario()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Genera el código del acta de entrega
     */
    public static function generarCodigo()
    {
        $anio = date('Y');
        $mes = date('m');
        $ultimaActa = self::whereYear('created_at', $anio)
            ->whereMonth('created_at', $mes)
            ->orderBy('id', 'desc')
            ->first();
        $numero = $ultimaActa ? intval(substr($ultimaActa->codigo, -6)) + 1 : 1;
        return 'ACT-' . $anio . $mes . str_pad($numero, 6, '0', STR_PAD_LEFT);
    }
}