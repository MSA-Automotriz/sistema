<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DocumentoSunarp extends Model
{
    use HasFactory;

    protected $table = 'documentos_sunarp';

    protected $fillable = [
        'cotizacion_id',
        'nombre',
        'tipo',
        'fecha',
        'archivo',
        'observaciones',
        'user_id'
    ];

    protected $casts = [
        'fecha' => 'date'
    ];

    /**
     * Obtiene la cotización asociada al documento SUNARP
     */
    public function cotizacion()
    {
        return $this->belongsTo(Cotizacion::class);
    }

    /**
     * Obtiene el usuario que creó el documento
     */
    public function usuario()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}