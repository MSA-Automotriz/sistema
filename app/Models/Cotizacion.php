<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cotizacion extends Model
{
    use HasFactory;
    
    protected $table = 'cotizaciones';
    
    protected $fillable = [
        'codigo',
        'cliente_id',
        'almacen_id',
        'condicion',
        'canal',
        'moneda',
        'forma_pago',
        'datos_adicionales',
        'subtotal',
        'impuestos',
        'total',
        'estado_id',
        'user_id',
        'fecha_validez',
        'gestionado',
        'centro_costo_id',
        'regla_vencimiento_id',
        'fecha_ultimo_seguimiento',
        'fecha_vencimiento',
        'fecha_alerta',
        'vencida',
        'reasignable',
        'historial_vencimiento'
    ];
    
    protected $casts = [
        'fecha_validez' => 'date',
        'gestionado' => 'boolean',
        'fecha_ultimo_seguimiento' => 'datetime',
        'fecha_vencimiento' => 'datetime', 
        'fecha_alerta' => 'datetime',
        'vencida' => 'boolean',
        'reasignable' => 'boolean',
        'historial_vencimiento' => 'array'
    ];
    
    // Relaciones principales
    public function cliente()
    {
        return $this->belongsTo(Cliente::class, 'cliente_id');
    }
    
    public function almacen()
    {
        return $this->belongsTo(Almacen::class, 'almacen_id');
    }
    
    public function estado()
    {
        return $this->belongsTo(EstadoCotizacion::class, 'estado_id');
    }
    
    public function usuario()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
    
    // Relaciones de detalles y seguimientos
    public function detalles()
    {
        return $this->hasMany(DetalleCotizacion::class, 'cotizacion_id');
    }
    
    public function historial()
    {
        return $this->hasMany(HistorialCotizacion::class, 'cotizacion_id')->orderBy('created_at', 'desc');
    }
    
    public function seguimientos()
    {
        return $this->hasMany(SeguimientoCotizacion::class, 'cotizacion_id')->latest();
    }
    
    // Relaciones de pagos y comprobantes
    public function pagos()
    {
        return $this->hasMany(Pago::class, 'cotizacion_id');
    }
    
    public function comprobantes()
    {
        return $this->hasMany(Comprobante::class, 'cotizacion_id');
    }
    
    // Relaciones de procesos (pestañas)
    public function nota_pedido()
    {
        return $this->hasOne(NotaPedido::class, 'cotizacion_id');
    }
    
    public function ordenTrabajo()
    {
        return $this->hasOne(OrdenTrabajo::class, 'cotizacion_id');
    }
    
    public function acta_entrega()
    {
        return $this->hasOne(ActaEntrega::class, 'cotizacion_id');
    }
    
    public function documentos_sunarp()
    {
        return $this->hasMany(DocumentoSunarp::class, 'cotizacion_id');
    }
    
    public function placa_info()
    {
        return $this->hasOne(PlacaInfo::class, 'cotizacion_id');
    }
    
    public function documentos_placa()
    {
        return $this->hasMany(DocumentoPlaca::class, 'cotizacion_id');
    }
    
    public function documentos()
    {
        return $this->hasMany(Documento::class, 'cotizacion_id');
    }
    
    // Relación con regla de vencimiento
    public function reglaVencimiento()
    {
        return $this->belongsTo(ReglaVencimientoCotizacion::class, 'regla_vencimiento_id');
    }
    
    // Relación con requerimiento de compra
    public function requerimientoCompra()
    {
        return $this->hasOne(RequerimientoCompra::class, 'cotizacion_id');
    }
    
    // Métodos de utilidad
    public static function generarCodigo()
    {
        $anio = date('Y');
        $mes = date('m');
        $ultimaCotizacion = self::whereYear('created_at', $anio)
            ->whereMonth('created_at', $mes)
            ->orderBy('id', 'desc')
            ->first();
        $numero = $ultimaCotizacion ? intval(substr($ultimaCotizacion->codigo, -6)) + 1 : 1;
        return 'COT-' . $anio . $mes . str_pad($numero, 6, '0', STR_PAD_LEFT);
    }
    
    // Scopes
    public function scopeEstado($query, $estadoId)
    {
        if ($estadoId) {
            return $query->where('estado_id', $estadoId);
        }
        return $query;
    }
    
    public function scopeCliente($query, $clienteId)
    {
        if ($clienteId) {
            return $query->where('cliente_id', $clienteId);
        }
        return $query;
    }
    
    public function scopeFechaRango($query, $desde, $hasta)
    {
        if ($desde && $hasta) {
            return $query->whereBetween('created_at', [$desde, $hasta]);
        }
        return $query;
    }
    public function placas()
    {
        return $this->hasMany(PlacaInfo::class, 'cotizacion_id');
    }
    public function centroCosto()
    {
        return $this->belongsTo(CentroCosto::class, 'centro_costo_id');
    }
    public function ultimoSeguimiento()
    {
        return $this->hasOne(SeguimientoCotizacion::class)->latest('fecha_seguimiento');
    }
    public function estadoAnterior()
    {
        return $this->belongsTo(EstadoCotizacion::class, 'estado_id');
    }  
    
    public function __call($method, $parameters)
    {
        if ($method === 'orden_trabajo') {
            return $this->ordenTrabajo();
        }
        
        return parent::__call($method, $parameters);
    }

    public function vehiculos()
    {
        return $this->hasManyThrough(
            Vehiculo::class, 
            DetalleCotizacion::class, 
            'cotizacion_id', // FK en detalles_cotizacion
            'id',            // PK en vehiculos
            'id',            // PK en cotizaciones
            'vehiculo_catalogo_id' // FK en detalles_cotizacion referenciando vehiculos
        );
    }
    public function orden_trabajo()
    {
        return $this->hasOne(OrdenTrabajo::class, 'cotizacion_id');
    }
    public function esCerradoGanado()
    {
        return $this->estado && $this->estado->nombre === 'Cerrado Ganado';
    }
    public function puedeGenerarRequerimiento()
    {
        return $this->esCerradoGanado() && !$this->requerimientoCompra && $this->detalles->count() > 0;
    }
    
    // Métodos de vencimiento
    public function aplicarReglaVencimiento(ReglaVencimientoCotizacion $regla)
    {
        $this->regla_vencimiento_id = $regla->id;
        $this->fecha_ultimo_seguimiento = now();
        $this->fecha_vencimiento = $regla->calcularFechaVencimiento();
        $this->fecha_alerta = $regla->calcularFechaAlerta();
        $this->vencida = false;
        $this->reasignable = false;
        
        $historial = $this->historial_vencimiento ?? [];
        $historial[] = [
            'fecha' => now()->toDateTimeString(),
            'accion' => 'regla_aplicada',
            'regla_id' => $regla->id,
            'regla_nombre' => $regla->nombre,
            'user_id' => auth()->id()
        ];
        $this->historial_vencimiento = $historial;
        
        $this->save();
    }
    
    public function actualizarUltimoSeguimiento()
    {
        $this->fecha_ultimo_seguimiento = now();
        
        if ($this->reglaVencimiento) {
            $this->fecha_vencimiento = $this->reglaVencimiento->calcularFechaVencimiento();
            $this->fecha_alerta = $this->reglaVencimiento->calcularFechaAlerta();
            $this->vencida = false;
            $this->reasignable = false;
        }
        
        $this->save();
    }
    
    public function marcarComoVencida()
    {
        if (!$this->reglaVencimiento) {
            return false;
        }
        
        $this->vencida = true;
        $this->reasignable = $this->reglaVencimiento->permite_reasignacion;
        
        // Cambiar estado si está configurado
        if ($this->reglaVencimiento->estado_vencido_id) {
            $this->estado_id = $this->reglaVencimiento->estado_vencido_id;
        }
        
        $historial = $this->historial_vencimiento ?? [];
        $historial[] = [
            'fecha' => now()->toDateTimeString(),
            'accion' => 'cotizacion_vencida',
            'user_id' => null // Sistema automático
        ];
        $this->historial_vencimiento = $historial;
        
        $this->save();
        
        return true;
    }
    
    public function puedeSerReasignada($nuevoUsuarioId)
    {
        if (!$this->vencida || !$this->reasignable) {
            return false;
        }
        
        if (!$this->reglaVencimiento) {
            return false;
        }
        
        // Si requiere aprobación, verificar lógica adicional
        if ($this->reglaVencimiento->requiere_aprobacion) {
            // Implementar lógica de aprobación según sea necesario
            return false; // Por ahora false hasta implementar aprobaciones
        }
        
        return true;
    }
    
    public function reasignar($nuevoUsuarioId)
    {
        if (!$this->puedeSerReasignada($nuevoUsuarioId)) {
            return false;
        }
        
        $usuarioAnterior = $this->user_id;
        $this->user_id = $nuevoUsuarioId;
        $this->vencida = false;
        $this->reasignable = false;
        $this->fecha_ultimo_seguimiento = now();
        
        if ($this->reglaVencimiento) {
            $this->fecha_vencimiento = $this->reglaVencimiento->calcularFechaVencimiento();
            $this->fecha_alerta = $this->reglaVencimiento->calcularFechaAlerta();
        }
        
        $historial = $this->historial_vencimiento ?? [];
        $historial[] = [
            'fecha' => now()->toDateTimeString(),
            'accion' => 'cotizacion_reasignada',
            'usuario_anterior' => $usuarioAnterior,
            'usuario_nuevo' => $nuevoUsuarioId,
            'user_id' => auth()->id()
        ];
        $this->historial_vencimiento = $historial;
        
        $this->save();
        
        return true;
    }
    
    // Scopes para consultas de vencimiento
    public function scopeVencidas($query)
    {
        return $query->where('vencida', true);
    }
    
    public function scopeReasignables($query)
    {
        return $query->where('reasignable', true);
    }
    
    public function scopeProximasAVencer($query, $dias = 3)
    {
        return $query->where('fecha_alerta', '<=', now())
                    ->where('vencida', false);
    }
    
    public function scopeConReglaVencimiento($query)
    {
        return $query->whereNotNull('regla_vencimiento_id');
    }
}