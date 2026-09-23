<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Parte extends Model
{
    use HasFactory;
    
    protected $table = 'partes';
    
    protected $fillable = [
        'codigo',
        'autogenerar_codigo',
        'nombre',
        'marca',
        'codigo_oem',
        'imagen',
        'unidad_id',
        'fabricante_id',
        'precio_venta',
        'moneda_venta',
        'precio_compra',
        'moneda_compra',
        'categoria_parte_id',
        'proveedor_id',
    ];

    protected $casts = [
        'precio_venta' => 'decimal:2',
        'precio_compra' => 'decimal:2',
        'autogenerar_codigo' => 'boolean',
    ];

    // Relaciones
    public function unidad()
    {
        return $this->belongsTo(Unidad::class);
    }

    public function fabricante()
    {
        return $this->belongsTo(Fabricante::class);
    }

    public function categoriaParte()
    {
        return $this->belongsTo(CategoriasPartes::class, 'categoria_parte_id');
    }

    public function proveedor()
    {
        return $this->belongsTo(Proveedor::class);
    }

    // Relación con inventarios
    public function inventarios()
    {
        return $this->hasMany(Inventario::class, 'parte_id');
    }

    // Relación con ventas
    public function ventas()
    {
        return $this->belongsToMany(Venta::class, 'detalles_venta', 'parte_id', 'venta_id')
            ->withPivot('cantidad', 'precio_unitario', 'descuento', 'subtotal', 'total');
    }

    /**
     * Obtener stock disponible en un almacén específico
     */
    public function stockEnAlmacen($almacenId)
    {
        $inventario = $this->inventarios()
            ->where('almacen_id', $almacenId)
            ->first();
            
        return $inventario ? $inventario->stock_disponible : 0;
    }

    /**
     * Obtener stock real en un almacén específico (descontando reservado)
     */
    public function stockRealEnAlmacen($almacenId)
    {
        $inventario = $this->inventarios()
            ->where('almacen_id', $almacenId)
            ->first();
            
        return $inventario ? $inventario->getStockRealAttribute() : 0;
    }

    /**
     * Verificar si tiene stock suficiente en un almacén
     */
    public function tieneStockEnAlmacen($almacenId, $cantidadRequerida = 1)
    {
        return $this->stockEnAlmacen($almacenId) >= $cantidadRequerida;
    }

    /**
     * Obtener inventario de un almacén específico
     */
    public function inventarioEnAlmacen($almacenId)
    {
        return $this->inventarios()
            ->where('almacen_id', $almacenId)
            ->first();
    }

    /**
     * Obtener todos los almacenes donde hay stock de esta parte
     */
    public function almacenesConStock()
    {
        return $this->inventarios()
            ->where('stock_disponible', '>', 0)
            ->with('almacen')
            ->get()
            ->pluck('almacen');
    }

    /**
     * Obtener stock total en todos los almacenes
     */
    public function stockTotal()
    {
        return $this->inventarios()->sum('stock_disponible');
    }

    /**
     * Scope para filtrar partes con precio de venta
     */
    public function scopeConPrecioVenta($query)
    {
        return $query->whereNotNull('precio_venta')->where('precio_venta', '>', 0);
    }

    /**
     * Scope para filtrar por categoría
     */
    public function scopePorCategoria($query, $categoriaId)
    {
        return $query->where('categoria_parte_id', $categoriaId);
    }

    /**
     * Scope para buscar por texto
     */
    public function scopeBuscar($query, $texto)
    {
        return $query->where(function($q) use ($texto) {
            $termino = '%' . $texto . '%';
            $q->where('nombre', 'like', $termino)
              ->orWhere('codigo', 'like', $termino);
              
            // Solo buscar en campos que existen
            if (\Schema::hasColumn('partes', 'marca')) {
                $q->orWhere('marca', 'like', $termino);
            }
            if (\Schema::hasColumn('partes', 'codigo_oem')) {
                $q->orWhere('codigo_oem', 'like', $termino);
            }
        });
    }

    /**
     * Scope para partes con stock en un almacén específico
     */
    public function scopeConStockEnAlmacen($query, $almacenId)
    {
        return $query->whereHas('inventarios', function($q) use ($almacenId) {
            $q->where('almacen_id', $almacenId)
              ->where('stock_disponible', '>', 0);
        });
    }

    /**
     * Obtener el precio formateado
     */
    public function getPrecioFormateadoAttribute()
    {
        if (!$this->precio_venta) return 'N/A';
        
        $moneda = $this->moneda_venta === 'Dólares' ? 'US$' : 'S/';
        return $moneda . ' ' . number_format($this->precio_venta, 2);
    }

    /**
     * Obtener información completa para el POS
     */
    public function getInfoPOS($almacenId)
    {
        $inventario = $this->inventarioEnAlmacen($almacenId);
        $stockDisponible = $inventario ? $inventario->stock_disponible : 0;
        
        return [
            'id' => $this->id,
            'codigo' => $this->codigo ?? 'SIN-CODIGO',
            'nombre' => $this->nombre ?? 'Sin nombre',
            'descripcion' => '', // Tu estructura no tiene descripción
            'precio' => $this->precio_venta ?? 0,
            'moneda' => $this->moneda_venta ?? 'SOL',
            'unidad' => $this->unidad ? $this->unidad->nombre : 'Unidad',
            'categoria' => $this->categoriaParte ? $this->categoriaParte->nombre : 'Sin categoría',
            'stock_disponible' => $stockDisponible,
            'stock_real' => $inventario ? $inventario->getStockRealAttribute() : 0,
            'tipo' => 'parte',
            'tiene_stock' => $stockDisponible > 0,
            'marca' => $this->marca ?? '',
            'codigo_oem' => $this->codigo_oem ?? '',
            'imagen' => $this->imagen ?? null
        ];
    }

    /**
     * Método estático para búsqueda optimizada para POS
     */
    public static function buscarParaPOS($query, $almacenId, $categoriaId = null, $incluirSinStock = true)
    {
        $partesQuery = self::with(['unidad', 'categoriaParte'])
            ->conPrecioVenta();

        // Aplicar filtros
        if (!empty($query)) {
            $partesQuery->buscar($query);
        }

        if ($categoriaId) {
            $partesQuery->porCategoria($categoriaId);
        }

        if (!$incluirSinStock) {
            $partesQuery->conStockEnAlmacen($almacenId);
        }

        return $partesQuery->get()->map(function($parte) use ($almacenId) {
            return $parte->getInfoPOS($almacenId);
        });
    }
}