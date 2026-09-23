<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Inventario extends Model
{
    protected $fillable = [
        'parte_id',
        'vehiculo_id',
        'almacen_id',
        'centro_costo_id',
        'stock_disponible',
        'stock_reservado',
        'stock_minimo',
        'stock_maximo',
        'ubicacion'
    ];

    protected $casts = [
        'stock_disponible' => 'integer',
        'stock_reservado' => 'integer',
        'stock_minimo' => 'integer',
        'stock_maximo' => 'integer',
    ];

    // Relaciones
    public function parte() {
        return $this->belongsTo(Parte::class);
    }

    public function vehiculo() {
        return $this->belongsTo(Vehiculo::class);
    }

    public function almacen() {
        return $this->belongsTo(Almacen::class);
    }

    public function centroCosto() {
        return $this->belongsTo(CentroCosto::class);
    }

    /**
     * Stock real disponible para venta (después de restar reservado)
     */
    public function getStockRealAttribute()
    {
        return max(0, $this->stock_disponible - ($this->stock_reservado ?? 0));
    }

    /**
     * Stock total esperado incluyendo órdenes pendientes
     */
    public function getStockEsperadoAttribute()
    {
        return $this->stock_disponible + $this->getOrdenesPendientesAttribute();
    }

    /**
     * Cantidad en órdenes de compra pendientes
     */
    public function getOrdenesPendientesAttribute()
    {
        try {
            if (!$this->parte_id) return 0;
            
            return DB::table('detalle_orden_compras as doc')
                ->join('orden_compras as oc', 'doc.orden_compra_id', '=', 'oc.id')
                ->where('doc.item_id', $this->parte_id)
                ->where('doc.tipo_item', 'parte')
                ->where('oc.almacen_destino_id', $this->almacen_id)
                ->whereIn('oc.estado', ['APROBADA', 'EN PROCESO'])
                ->sum('doc.cantidad_en_compra') ?? 0;
        } catch (\Exception $e) {
            return 0;
        }
    }

    /**
     * Validar si hay stock suficiente para una cantidad requerida
     */
    public function tieneStockSuficiente($cantidadRequerida)
    {
        return $this->stock_disponible >= $cantidadRequerida;
    }

    /**
     * Validar si hay stock real suficiente (descontando reservado)
     */
    public function tieneStockRealSuficiente($cantidadRequerida)
    {
        return $this->getStockRealAttribute() >= $cantidadRequerida;
    }

    /**
     * Reservar stock para una venta
     */
    public function reservarStock($cantidad)
    {
        if (!$this->tieneStockSuficiente($cantidad)) {
            return false;
        }

        $this->stock_reservado = ($this->stock_reservado ?? 0) + $cantidad;
        return $this->save();
    }

    /**
     * Liberar stock reservado
     */
    public function liberarStock($cantidad)
    {
        $this->stock_reservado = max(0, ($this->stock_reservado ?? 0) - $cantidad);
        return $this->save();
    }

    /**
     * Confirmar venta (reduce stock disponible y libera reservado)
     */
    public function confirmarVenta($cantidad)
    {
        if (!$this->tieneStockSuficiente($cantidad)) {
            return false;
        }

        $this->stock_disponible -= $cantidad;
        $this->stock_reservado = max(0, ($this->stock_reservado ?? 0) - $cantidad);
        
        return $this->save();
    }

    /**
     * Movimientos de kardex relacionados
     */
    public function movimientosKardex()
    {
        if ($this->parte_id) {
            return DB::table('kardex')
                ->where('parte_id', $this->parte_id)
                ->where('almacen_id', $this->almacen_id)
                ->whereNull('vehiculo_id');
        } else {
            return DB::table('kardex')
                ->where('vehiculo_id', $this->vehiculo_id)
                ->where('almacen_id', $this->almacen_id)
                ->whereNull('parte_id');
        }
    }

    /**
     * Último movimiento registrado
     */
    public function ultimoMovimiento()
    {
        return $this->movimientosKardex()->orderBy('created_at', 'desc')->first();
    }

    /**
     * Costo promedio de la parte
     */
    public function costoPromedio()
    {
        try {
            $movimientos = $this->movimientosKardex()
                ->where('tipo_movimiento', 'ENTRADA')
                ->get();

            if ($movimientos->isEmpty()) {
                return 0;
            }

            $totalCosto = $movimientos->sum('valor_total');
            $totalCantidad = $movimientos->sum('cantidad_entrada');

            return $totalCantidad > 0 ? $totalCosto / $totalCantidad : 0;
        } catch (\Exception $e) {
            return 0;
        }
    }

    /**
     * Scope para filtrar por almacén
     */
    public function scopeEnAlmacen($query, $almacenId)
    {
        return $query->where('almacen_id', $almacenId);
    }

    /**
     * Scope para filtrar solo items con stock
     */
    public function scopeConStock($query)
    {
        return $query->where('stock_disponible', '>', 0);
    }

    /**
     * Scope para filtrar por parte
     */
    public function scopePorParte($query, $parteId)
    {
        return $query->where('parte_id', $parteId);
    }

    /**
     * Método estático para obtener stock disponible de una parte en un almacén
     */
    public static function stockDisponible($parteId, $almacenId)
    {
        $inventario = self::where('parte_id', $parteId)
            ->where('almacen_id', $almacenId)
            ->first();

        return $inventario ? $inventario->stock_disponible : 0;
    }

    /**
     * Método estático para verificar stock disponible
     */
    public static function verificarStock($parteId, $almacenId, $cantidadRequerida)
    {
        $stockDisponible = self::stockDisponible($parteId, $almacenId);
        return $stockDisponible >= $cantidadRequerida;
    }
}