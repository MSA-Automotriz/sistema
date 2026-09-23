<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RecepcionOrdenCompra extends Model
{
    use HasFactory;

    protected $table = 'recepciones_orden_compra';

    protected $fillable = [
        'detalle_orden_compra_id',
        'cantidad_recibida',
        'fecha_recepcion',
        'observaciones',
        'recibido_por'
    ];

    protected $casts = [
        'fecha_recepcion' => 'date'
    ];

    public function detalleOrdenCompra()
    {
        return $this->belongsTo(DetalleOrdenCompra::class);
    }

    public function recibidoPor()
    {
        return $this->belongsTo(User::class, 'recibido_por');
    }
    public function historial()
    {
        $recepciones = RecepcionOrdenCompra::with([
            'detalleOrdenCompra.ordenCompra.proveedor',
            'recibidoPor'
        ])->orderBy('fecha_recepcion', 'desc')->get();

        return view('admin.compras.documentos.recepcion.historial', compact('recepciones'));
    }
}